<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TotpAuthenticator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MfaAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_user_can_enable_application_mfa_from_security_page(): void
    {
        $user = User::factory()->create();
        $totp = app(TotpAuthenticator::class);

        $this->actingAs($user)
            ->withSecurityConfirmation($user)
            ->post(route('cabinet.security.mfa.start'))
            ->assertRedirect(route('cabinet.security'))
            ->assertSessionHas('mfa_setup.secret');

        $secret = session('mfa_setup.secret');
        $this->assertIsString($secret);

        $this->actingAs($user)
            ->get(route('cabinet.security'))
            ->assertOk()
            ->assertSee('Application MFA QR code')
            ->assertSee('Show setup URI')
            ->assertDontSee('Security roadmap');

        $this->actingAs($user)
            ->withSecurityConfirmation($user)
            ->post(route('cabinet.security.mfa.confirm'), [
                'code' => $totp->code($secret),
            ])
            ->assertRedirect(route('cabinet.security'))
            ->assertSessionHas('mfa_recovery_codes');

        $user->refresh();

        $this->assertTrue($user->hasMfaEnabled());
        $this->assertNotNull($user->mfa_recovery_codes);
        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'event' => 'security.mfa_enabled',
            'title' => 'Application MFA enabled',
        ]);
    }

    public function test_user_can_disable_application_mfa_after_recent_mfa_step_up(): void
    {
        $totp = app(TotpAuthenticator::class);
        $secret = $totp->generateSecret();
        $user = User::factory()->create([
            'mfa_secret' => $secret,
            'mfa_recovery_codes' => [],
            'mfa_confirmed_at' => now(),
        ]);

        $this->actingAs($user)
            ->withSecurityConfirmation($user, 'mfa')
            ->delete(route('cabinet.security.mfa.destroy'))
            ->assertRedirect(route('cabinet.security'))
            ->assertSessionHas('status', 'Application MFA disabled.');

        $user->refresh();

        $this->assertFalse($user->hasMfaEnabled());
        $this->assertNull($user->mfa_secret);
        $this->assertNull($user->mfa_recovery_codes);
        $this->assertNull($user->mfa_confirmed_at);
        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'event' => 'security.mfa_disabled',
            'title' => 'Application MFA disabled',
        ]);
    }

    public function test_password_login_requires_mfa_challenge_when_enabled(): void
    {
        $totp = app(TotpAuthenticator::class);
        $secret = $totp->generateSecret();
        $user = User::factory()->create([
            'email' => 'mfa@example.com',
            'password' => Hash::make('password'),
            'mfa_secret' => $secret,
            'mfa_recovery_codes' => [],
            'mfa_confirmed_at' => now(),
        ]);

        $this->post(route('login.store'), [
            'email' => 'mfa@example.com',
            'password' => 'password',
        ])
            ->assertRedirect(route('mfa.challenge'))
            ->assertSessionHas('auth.mfa.user_id', $user->id);

        $this->assertGuest();

        $this->post(route('mfa.challenge.store'), [
            'code' => $totp->code($secret),
        ])->assertRedirect(route('cabinet.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'event' => 'security.mfa_challenge_passed',
        ]);
    }

    public function test_invalid_mfa_challenge_code_does_not_authenticate_user(): void
    {
        $user = User::factory()->create([
            'mfa_secret' => app(TotpAuthenticator::class)->generateSecret(),
            'mfa_recovery_codes' => [],
            'mfa_confirmed_at' => now(),
        ]);

        $this->withSession([
            'auth.mfa.user_id' => $user->id,
            'auth.mfa.started_at' => now()->getTimestamp(),
        ])
            ->post(route('mfa.challenge.store'), [
                'code' => '000000',
            ])
            ->assertRedirect(route('mfa.challenge'))
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_recovery_code_can_complete_mfa_challenge_once(): void
    {
        $recoveryCode = 'ABCD-1234';
        $user = User::factory()->create([
            'mfa_secret' => app(TotpAuthenticator::class)->generateSecret(),
            'mfa_recovery_codes' => [Hash::make($recoveryCode)],
            'mfa_confirmed_at' => now(),
        ]);

        $this->withSession([
            'auth.mfa.user_id' => $user->id,
            'auth.mfa.started_at' => now()->getTimestamp(),
        ])
            ->post(route('mfa.challenge.store'), [
                'code' => strtolower($recoveryCode),
            ])
            ->assertRedirect(route('cabinet.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame([], $user->refresh()->mfa_recovery_codes);
    }

    public function test_mfa_disable_requires_recent_security_confirmation(): void
    {
        $totp = app(TotpAuthenticator::class);
        $secret = $totp->generateSecret();
        $user = User::factory()->create([
            'mfa_secret' => $secret,
            'mfa_recovery_codes' => [],
            'mfa_confirmed_at' => now(),
        ]);

        $this->actingAs($user)
            ->delete(route('cabinet.security.mfa.destroy'))
            ->assertRedirect(route('security.confirm'));

        $user->refresh();

        $this->assertTrue($user->hasMfaEnabled());
        $this->assertDatabaseMissing('activity_logs', [
            'subject_user_id' => $user->id,
            'event' => 'security.mfa_disabled',
        ]);
    }
}
