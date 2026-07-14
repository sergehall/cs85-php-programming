<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\MfaTokenVerifier;
use App\Services\TotpAuthenticator;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_registration_normalizes_email_requires_verification_and_sends_notification(): void
    {
        Notification::fake();

        $this->post(route('register.store'), [
            'name' => 'Normalized User',
            'email' => '  Mixed.Case@Example.COM ',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertRedirect(route('cabinet.dashboard'));

        $user = User::query()->where('email', 'mixed.case@example.com')->firstOrFail();

        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get(route('cabinet.dashboard'))->assertRedirect(route('verification.notice'));
    }

    public function test_signed_email_verification_unlocks_cabinet_and_is_audited(): void
    {
        $user = User::factory()->unverified()->create();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(30),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())],
        );

        $this->actingAs($user)->get($verificationUrl)->assertRedirect(route('cabinet.dashboard'));

        $this->assertTrue($user->refresh()->hasVerifiedEmail());
        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'event' => 'auth.email_verified',
        ]);
    }

    public function test_password_reset_is_case_normalized_and_revokes_existing_sessions(): void
    {
        Notification::fake();
        config(['session.driver' => 'database']);

        $user = User::factory()->create(['email' => 'reset@example.com']);
        $this->insertSession('other-session', $user);

        $this->post(route('password.email'), ['email' => 'RESET@EXAMPLE.COM'])
            ->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        $this->assertIsString($token);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'RESET@EXAMPLE.COM',
            'password' => 'NewStrongPassword123!',
            'password_confirmation' => 'NewStrongPassword123!',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('NewStrongPassword123!', $user->refresh()->password));
        $this->assertDatabaseMissing('sessions', ['id' => 'other-session']);
        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'event' => 'auth.password_reset',
        ]);
    }

    public function test_login_is_rate_limited_and_failed_attempts_are_audited_without_raw_password(): void
    {
        $user = User::factory()->create([
            'email' => 'throttle@example.com',
            'password' => 'StrongPassword123!',
        ]);

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login.store'), [
                'email' => 'THROTTLE@EXAMPLE.COM',
                'password' => 'WrongPassword123!',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), [
            'email' => 'throttle@example.com',
            'password' => 'WrongPassword123!',
        ])->assertTooManyRequests();

        $audit = ActivityLog::query()
            ->where('subject_user_id', $user->id)
            ->where('event', 'auth.login_failed')
            ->latest('id')
            ->firstOrFail();

        $metadata = $audit->getAttribute('metadata');
        $this->assertIsArray($metadata);
        $this->assertSame('failure', $metadata['outcome'] ?? null);
        $this->assertArrayHasKey('identity_hash', $metadata);
        $this->assertStringNotContainsString('WrongPassword123!', json_encode($metadata, JSON_THROW_ON_ERROR));
    }

    public function test_sensitive_password_change_requires_step_up_and_revokes_other_sessions(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->create(['password' => 'CurrentPassword123!']);
        $this->insertSession('other-session', $user);

        $this->actingAs($user)
            ->put(route('cabinet.security.password.update'), [
                'password' => 'ReplacementPassword123!',
                'password_confirmation' => 'ReplacementPassword123!',
            ])
            ->assertRedirect(route('security.confirm'));

        $this->actingAs($user)
            ->withSecurityConfirmation($user)
            ->put(route('cabinet.security.password.update'), [
                'password' => 'ReplacementPassword123!',
                'password_confirmation' => 'ReplacementPassword123!',
            ])
            ->assertRedirect(route('cabinet.security'));

        $this->assertTrue(Hash::check('ReplacementPassword123!', $user->refresh()->password));
        $this->assertDatabaseMissing('sessions', ['id' => 'other-session']);
    }

    public function test_password_step_up_records_success_and_rejects_invalid_proof(): void
    {
        $user = User::factory()->create(['password' => 'CurrentPassword123!']);

        $this->actingAs($user)
            ->post(route('security.confirm.store'), ['proof' => 'wrong-password'])
            ->assertSessionHasErrors('proof');

        $this->actingAs($user)
            ->post(route('security.confirm.store'), ['proof' => 'CurrentPassword123!'])
            ->assertRedirect(route('cabinet.security'));

        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'event' => 'security.step_up_failed',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'event' => 'security.step_up_succeeded',
        ]);
    }

    public function test_mfa_pending_challenge_expires_and_totp_cannot_be_replayed(): void
    {
        $totp = app(TotpAuthenticator::class);
        $secret = $totp->generateSecret();
        $user = User::factory()->create([
            'mfa_secret' => $secret,
            'mfa_recovery_codes' => [],
            'mfa_confirmed_at' => now(),
        ]);

        $this->withSession([
            'auth.mfa.user_id' => $user->id,
            'auth.mfa.started_at' => now()->subMinutes(10)->getTimestamp(),
        ])->post(route('mfa.challenge.store'), ['code' => $totp->code($secret)])
            ->assertRedirect(route('login'));

        $verifier = app(MfaTokenVerifier::class);
        $code = $totp->code($secret);

        $this->assertTrue($verifier->verifyAndConsume($user, $code));
        $this->assertFalse($verifier->verifyAndConsume($user, $code));
    }

    public function test_user_cannot_revoke_another_users_session(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->insertSession('other-user-session', $other);

        $this->actingAs($user)
            ->withSecurityConfirmation($user)
            ->delete(route('cabinet.security.sessions.destroy', 'other-user-session'))
            ->assertNotFound();

        $this->assertDatabaseHas('sessions', ['id' => 'other-user-session', 'user_id' => $other->id]);
    }

    private function insertSession(string $id, User $user): void
    {
        DB::table('sessions')->insert([
            'id' => $id,
            'user_id' => $user->getKey(),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit browser',
            'payload' => 'test-payload',
            'last_activity' => now()->getTimestamp(),
        ]);
    }
}
