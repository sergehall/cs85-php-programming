<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_login_page_offers_github_and_create_account_without_google(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Continue with GitHub');
        $response->assertSee('Create one');
        $response->assertDontSee('Continue with Google');
    }

    public function test_registration_creates_user_account_and_enters_cabinet(): void
    {
        $response = $this->post('/register', [
            'name' => 'Serge Hall',
            'email' => 'serge@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);

        $response->assertRedirect('/cabinet');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'serge@example.com',
            'role' => 'user',
        ]);
    }

    public function test_login_authenticates_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'StrongPassword123!',
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'StrongPassword123!',
        ]);

        $response->assertRedirect('/cabinet');
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_ends_current_session(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_only_github_oauth_route_is_registered(): void
    {
        $this->assertTrue(Route::has('auth.github.redirect'));
        $this->assertFalse(Route::has('auth.google.redirect'));
    }

    public function test_github_redirect_requires_configuration(): void
    {
        config([
            'services.github.client_id' => null,
            'services.github.client_secret' => null,
        ]);

        $response = $this->get('/auth/github/redirect');

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('github');
    }

    public function test_github_callback_creates_user_and_enters_cabinet(): void
    {
        config([
            'services.github.client_id' => 'client-id',
            'services.github.client_secret' => 'client-secret',
            'services.github.redirect' => 'http://localhost/auth/github/callback',
        ]);

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
                ['email' => 'serge.github@example.com', 'primary' => true, 'verified' => true],
            ], 200),
        ]);

        $response = $this
            ->withSession(['oauth.github_state' => 'known-state'])
            ->get('/auth/github/callback?state=known-state&code=github-code');

        $response->assertRedirect('/cabinet');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'serge.github@example.com',
            'github_id' => '12345',
            'github_username' => 'sergehall',
            'role' => 'user',
        ]);
    }
}
