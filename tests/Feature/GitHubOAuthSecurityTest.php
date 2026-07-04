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

    public function test_github_email_match_does_not_downgrade_existing_admin_role(): void
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
        ]);

        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertRedirect('/cabinet');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'github_id' => '12345',
            'role' => 'admin',
        ]);
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
        ]);

        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertSessionHasErrors('github');
        $response->assertSessionHasErrors([
            'github' => 'The GitHub account and verified GitHub email belong to different CS85 users. Sign in with the matching local account or use a different GitHub account.',
        ]);
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

        $response = $this->actingAs($user)->get('/auth/github/redirect');

        $response->assertRedirectContains('https://github.com/login/oauth/authorize');
        $response->assertSessionHas('oauth.github_state');
        $response->assertSessionHas('oauth.github_link_user_id', $user->getKey());
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
        ]);

        $response = $this
            ->actingAs($user)
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
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession([
                'oauth.github_state' => 'known-state',
                'oauth.github_link_user_id' => $user->getKey(),
            ])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertSessionHasErrors('github');
        $response->assertSessionHasErrors([
            'github' => 'The GitHub account you authorized is already connected to another CS85 user. Sign out of GitHub or choose a different GitHub account.',
        ]);
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
