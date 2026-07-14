<?php

namespace Tests\Feature;

use App\Models\AdminAccessRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_standard_user_can_request_admin_access_from_security_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('cabinet.security.admin-access-request'));

        $response->assertRedirect(route('cabinet.security'));
        $response->assertSessionHas('status', 'Admin access request sent for review.');
        $this->assertDatabaseHas('admin_access_requests', [
            'user_id' => $user->id,
            'status' => AdminAccessRequest::STATUS_PENDING,
            'reviewed_by' => null,
        ]);

        $this->actingAs($user)
            ->get(route('cabinet.security'))
            ->assertOk()
            ->assertSee('Request pending')
            ->assertSee('Pending admin review');
    }

    public function test_standard_user_cannot_create_duplicate_pending_request(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->post(route('cabinet.security.admin-access-request'));
        $response = $this->actingAs($user)->post(route('cabinet.security.admin-access-request'));

        $response->assertRedirect(route('cabinet.security'));
        $response->assertSessionHas('status', 'Your admin access request is already pending review.');
        $this->assertSame(1, AdminAccessRequest::query()->where('user_id', $user->id)->count());
    }

    public function test_standard_user_cannot_review_admin_access_requests(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $requester = User::factory()->create(['role' => 'user']);
        $accessRequest = AdminAccessRequest::factory()->create(['user_id' => $requester->id]);

        $this->actingAs($user)
            ->patch(route('cabinet.admin.access-requests.approve', $accessRequest))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $requester->id,
            'role' => 'user',
        ]);
    }

    public function test_admin_can_grant_requested_admin_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $requester = User::factory()->create(['role' => 'user']);
        $accessRequest = AdminAccessRequest::factory()->create(['user_id' => $requester->id]);

        $response = $this->actingAs($admin)
            ->withSecurityConfirmation($admin)
            ->patch(route('cabinet.admin.access-requests.approve', $accessRequest));

        $response->assertRedirect(route('cabinet.admin.users'));
        $response->assertSessionHas('status', 'Admin access granted.');
        $this->assertDatabaseHas('users', [
            'id' => $requester->id,
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('admin_access_requests', [
            'id' => $accessRequest->id,
            'status' => AdminAccessRequest::STATUS_APPROVED,
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_admin_users_page_shows_pending_requests_and_role_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
        $requester = User::factory()->create(['role' => 'user', 'name' => 'Requesting User']);
        AdminAccessRequest::factory()->create(['user_id' => $requester->id]);

        $response = $this->actingAs($admin)->get(route('cabinet.admin.users'));

        $response->assertOk();
        $response->assertSee('Admin access requests');
        $response->assertSee('Requesting User');
        $response->assertSee('Grant admin access');
        $response->assertSee('Current admin');
    }

    public function test_admin_users_page_filters_accounts_by_search_role_security_and_request_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
        $pendingUser = User::factory()->create([
            'role' => 'user',
            'name' => 'Pending Student',
            'email' => 'pending@example.com',
            'github_id' => 'github-123',
            'github_username' => 'pendinghub',
        ]);
        $mfaUser = User::factory()->create([
            'role' => 'user',
            'name' => 'Mfa Student',
            'email' => 'mfa@example.com',
            'mfa_secret' => 'secret',
            'mfa_confirmed_at' => now(),
        ]);

        AdminAccessRequest::factory()->create(['user_id' => $pendingUser->id]);

        $response = $this->actingAs($admin)->get(route('cabinet.admin.users', [
            'search' => 'pendinghub',
            'role' => 'user',
            'security' => 'github_connected',
            'request' => AdminAccessRequest::STATUS_PENDING,
        ]));

        $response->assertOk();
        $response->assertSee('User directory');
        $response->assertSee('1 matching');
        $response->assertSee('Pending Student');
        $response->assertSee('pending@example.com');
        $response->assertSee('GitHub connected');
        $response->assertSee('Request: Pending');
        $response->assertDontSee('Mfa Student');
        $response->assertDontSee('mfa@example.com');

        $this->actingAs($admin)->get(route('cabinet.admin.users', [
            'security' => 'mfa_enabled',
            'request' => 'none',
        ]))
            ->assertOk()
            ->assertSee('1 matching')
            ->assertSee('Mfa Student')
            ->assertSee('MFA enabled')
            ->assertDontSee('pendinghub');
    }

    public function test_admin_can_disable_and_restore_standard_user_login_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'name' => 'Blocked Student']);

        $disableResponse = $this->actingAs($admin)
            ->withSecurityConfirmation($admin)
            ->patch(route('cabinet.admin.users.disable-login', $user));

        $disableResponse->assertRedirect(route('cabinet.admin.users'));
        $disableResponse->assertSessionHas('status', 'User login access disabled.');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'login_enabled' => false,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'actor_user_id' => $admin->id,
            'event' => 'user_login.disabled',
            'title' => 'User login disabled',
        ]);

        $this->actingAs($admin)
            ->get(route('cabinet.admin.users', ['security' => 'login_blocked']))
            ->assertOk()
            ->assertSee('Blocked Student')
            ->assertSee('Login blocked')
            ->assertSee('Allow login');

        $enableResponse = $this->actingAs($admin)
            ->withSecurityConfirmation($admin)
            ->patch(route('cabinet.admin.users.enable-login', $user));

        $enableResponse->assertRedirect(route('cabinet.admin.users'));
        $enableResponse->assertSessionHas('status', 'User login access enabled.');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'login_enabled' => true,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'actor_user_id' => $admin->id,
            'event' => 'user_login.enabled',
            'title' => 'User login enabled',
        ]);
    }

    public function test_admin_login_access_action_does_not_disable_admin_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->withSecurityConfirmation($admin)
            ->patch(route('cabinet.admin.users.disable-login', $targetAdmin));

        $response->assertRedirect(route('cabinet.admin.users'));
        $response->assertSessionHasErrors('login');
        $this->assertDatabaseHas('users', [
            'id' => $targetAdmin->id,
            'login_enabled' => true,
        ]);
    }

    public function test_admin_can_revoke_another_admin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->withSecurityConfirmation($admin)
            ->patch(route('cabinet.admin.users.revoke-admin', $target));

        $response->assertRedirect(route('cabinet.admin.users'));
        $response->assertSessionHas('status', 'Admin access revoked.');
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'role' => 'user',
        ]);
        $this->assertDatabaseHas('admin_access_requests', [
            'user_id' => $target->id,
            'status' => AdminAccessRequest::STATUS_REVOKED,
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_admin_cannot_revoke_own_admin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->withSecurityConfirmation($admin)
            ->patch(route('cabinet.admin.users.revoke-admin', $admin));

        $response->assertRedirect(route('cabinet.admin.users'));
        $response->assertSessionHasErrors('role');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }
}
