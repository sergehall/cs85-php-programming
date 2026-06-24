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

    private function configureGithubOAuth(): void
    {
        config([
            'services.github.client_id' => 'client-id',
            'services.github.client_secret' => 'client-secret',
            'services.github.redirect' => 'http://localhost/auth/github/callback',
        ]);
    }
}
