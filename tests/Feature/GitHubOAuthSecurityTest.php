<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GitHubOAuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->configureGithubOAuth();
    }

    public function test_github_callback_rejects_missing_session_state(): void
    {
        $response = $this->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertSessionHasErrors('github');
        $this->assertGuest();
    }

    public function test_github_callback_rejects_state_mismatch(): void
    {
        $response = $this
            ->withSession(['oauth.github_state' => 'expected-state'])
            ->get('/auth/github/callback?state=wrong-state&code=github-code');

        $response->assertSessionHasErrors('github');
        $this->assertGuest();
    }

    public function test_github_callback_rejects_provider_error_response(): void
    {
        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&error=access_denied');

        $response->assertSessionHasErrors('github');
        $this->assertGuest();
    }

    public function test_github_callback_rejects_failed_token_exchange(): void
    {
        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['error' => 'bad_verification_code'], 400),
        ]);

        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertSessionHasErrors('github');
        $this->assertGuest();
    }

    public function test_github_callback_rejects_failed_profile_fetch(): void
    {
        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['access_token' => 'github-token'], 200),
            'api.github.com/user' => Http::response(['message' => 'Bad credentials'], 401),
        ]);

        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertSessionHasErrors('github');
        $this->assertGuest();
    }

    public function test_github_callback_rejects_profiles_without_usable_email(): void
    {
        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['access_token' => 'github-token'], 200),
            'api.github.com/user' => Http::response([
                'id' => 12345,
                'login' => 'sergehall',
                'name' => 'Serge Hall',
                'email' => null,
                'avatar_url' => 'https://avatars.githubusercontent.com/u/12345',
            ], 200),
            'api.github.com/user/emails' => Http::response([
                ['email' => 'private@example.com', 'primary' => true, 'verified' => false],
            ], 200),
        ]);

        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertSessionHasErrors('github');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['github_id' => '12345']);
    }

    public function test_github_email_match_requires_explicit_linking_for_existing_admin(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['access_token' => 'github-token'], 200),
            'api.github.com/user' => Http::response([
                'id' => 12345,
                'login' => 'sergehall',
                'name' => 'Serge Hall',
                'email' => 'admin@example.com',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/12345',
            ], 200),
            'api.github.com/user/emails' => Http::response([
                ['email' => 'admin@example.com', 'primary' => true, 'verified' => true],
            ], 200),
        ]);

        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('github');
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'github_id' => null,
            'role' => 'admin',
        ]);
    }

    public function test_github_callback_rejects_account_disabled_by_admin(): void
    {
        User::factory()->create([
            'email' => 'blocked@example.com',
            'github_id' => '12345',
            'login_enabled' => false,
        ]);

        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['access_token' => 'github-token'], 200),
            'api.github.com/user' => Http::response([
                'id' => 12345,
                'login' => 'blockedhub',
                'name' => 'Blocked User',
                'email' => 'blocked@example.com',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/12345',
            ], 200),
            'api.github.com/user/emails' => Http::response([
                ['email' => 'blocked@example.com', 'primary' => true, 'verified' => true],
            ], 200),
        ]);

        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'github' => 'This account is not allowed to sign in right now. Contact an administrator.',
        ]);
        $this->assertGuest();
    }

    public function test_github_callback_rejects_identity_and_email_conflict_between_users(): void
    {
        User::factory()->create([
            'github_id' => '12345',
            'email' => 'github-owner@example.com',
        ]);
        User::factory()->create([
            'email' => 'email-owner@example.com',
        ]);

        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['access_token' => 'github-token'], 200),
            'api.github.com/user' => Http::response([
                'id' => 12345,
                'login' => 'sergehall',
                'name' => 'Serge Hall',
                'email' => 'email-owner@example.com',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/12345',
            ], 200),
            'api.github.com/user/emails' => Http::response([
                ['email' => 'email-owner@example.com', 'primary' => true, 'verified' => true],
            ], 200),
        ]);

        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertSessionHasErrors('github');
        $response->assertSessionHasErrors([
            'github' => 'We could not connect that GitHub account. Sign in to the correct GitHub account on github.com or use a private browser window, then try again.',
        ]);
        $this->assertStringNotContainsString('another', session('errors')->first('github'));
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'github_id' => '12345',
            'email' => 'github-owner@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'github_id' => null,
            'email' => 'email-owner@example.com',
        ]);
    }

    public function test_authenticated_user_can_start_github_account_linking(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSecurityConfirmation($user)
            ->get('/auth/github/redirect');

        $response->assertRedirectContains('https://github.com/login/oauth/authorize');
        $response->assertSessionHas('oauth.github_state');
        $response->assertSessionHas('oauth.github_link_user_id', $user->getKey());
    }

    public function test_authenticated_github_link_requires_recent_step_up(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/auth/github/redirect')
            ->assertRedirect(route('security.confirm'));
    }

    public function test_oauth_only_user_can_complete_step_up_with_matching_github_identity(): void
    {
        $user = User::factory()->create([
            'github_id' => '12345',
            'password_login_enabled' => false,
        ]);

        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['access_token' => 'github-token'], 200),
            'api.github.com/user' => Http::response([
                'id' => 12345,
                'login' => 'sergehall',
                'name' => 'Serge Hall',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/12345',
            ], 200),
            'api.github.com/user/emails' => Http::response([
                ['email' => $user->email, 'primary' => true, 'verified' => true],
            ], 200),
        ]);

        $this->actingAs($user)
            ->withSession([
                'oauth.github_state' => 'known-state',
                'oauth.github_purpose' => 'step_up',
                'oauth.github_link_user_id' => $user->getKey(),
            ])
            ->get('/auth/github/callback?state=known-state&code=github-code')
            ->assertRedirect(route('cabinet.security'))
            ->assertSessionHas('auth.security_confirmation.method', 'github');

        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'event' => 'security.step_up_succeeded',
        ]);
    }

    public function test_authenticated_user_can_link_github_identity_without_replacing_local_email(): void
    {
        $user = User::factory()->create([
            'email' => 'local@example.com',
        ]);

        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['access_token' => 'github-token'], 200),
            'api.github.com/user' => Http::response([
                'id' => 67890,
                'login' => 'sergehall',
                'name' => 'Serge Hall',
                'email' => 'github@example.com',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/67890',
            ], 200),
            'api.github.com/user/emails' => Http::response([
                ['email' => 'github@example.com', 'primary' => true, 'verified' => true],
            ], 200),
        ]);

        $response = $this
            ->actingAs($user)
            ->withSecurityConfirmation($user)
            ->withSession([
                'oauth.github_state' => 'known-state',
                'oauth.github_link_user_id' => $user->getKey(),
            ])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertRedirect(route('cabinet.security'));
        $response->assertSessionHas('status', 'GitHub account connected successfully.');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'local@example.com',
            'github_id' => '67890',
            'github_username' => 'sergehall',
            'github_avatar_url' => 'https://avatars.githubusercontent.com/u/67890',
        ]);
    }

    public function test_authenticated_user_cannot_link_github_identity_owned_by_another_account(): void
    {
        User::factory()->create([
            'github_id' => '67890',
            'email' => 'owner@example.com',
        ]);
        $user = User::factory()->create([
            'email' => 'local@example.com',
        ]);

        Http::fake([
            'github.com/login/oauth/access_token' => Http::response(['access_token' => 'github-token'], 200),
            'api.github.com/user' => Http::response([
                'id' => 67890,
                'login' => 'sergehall',
                'name' => 'Serge Hall',
                'email' => 'github@example.com',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/67890',
            ], 200),
            'api.github.com/user/emails' => Http::response([
                ['email' => 'github@example.com', 'primary' => true, 'verified' => true],
            ], 200),
        ]);

        $response = $this
            ->actingAs($user)
            ->withSecurityConfirmation($user)
            ->withSession([
                'oauth.github_state' => 'known-state',
                'oauth.github_link_user_id' => $user->getKey(),
            ])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertSessionHasErrors('github');
        $response->assertSessionHasErrors([
            'github' => 'We could not connect that GitHub account. Sign in to the correct GitHub account on github.com or use a private browser window, then try again.',
        ]);
        $this->assertStringNotContainsString('already connected', session('errors')->first('github'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'local@example.com',
            'github_id' => null,
        ]);
    }

    private function configureGithubOAuth(): void
    {
        config([
            'services.github.client_id' => 'client-id',
            'services.github.client_secret' => 'client-secret',
            'services.github.redirect' => 'http://localhost/auth/github/callback',
        ]);
    }
}
