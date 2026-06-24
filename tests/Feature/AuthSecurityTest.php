<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_login_rejects_invalid_password_without_authenticating_user(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'StrongPassword123!',
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_rejects_weak_passwords(): void
    {
        $response = $this->post('/register', [
            'name' => 'Serge Hall',
            'email' => 'weak@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'weak@example.com']);
    }

    public function test_registration_rejects_duplicate_email_addresses(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Duplicate User',
            'email' => 'existing@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_rejects_password_confirmation_mismatch(): void
    {
        $response = $this->post('/register', [
            'name' => 'Mismatch User',
            'email' => 'mismatch@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    }

    public function test_guest_cannot_post_logout_route(): void
    {
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }
}
