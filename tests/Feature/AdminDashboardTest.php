<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\AdminAccessRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_overview_uses_live_operational_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
        $requester = User::factory()->create(['role' => 'user', 'email' => 'requester@example.com']);
        User::factory()->create(['role' => 'user', 'github_id' => '12345']);
        User::factory()->create([
            'role' => 'user',
            'mfa_secret' => 'secret',
            'mfa_confirmed_at' => now(),
        ]);

        AdminAccessRequest::factory()->create(['user_id' => $requester->id]);

        ActivityLog::factory()->create([
            'subject_user_id' => $requester->id,
            'actor_user_id' => $admin->id,
            'category' => 'admin',
            'event' => 'admin_access.granted',
            'visibility' => ActivityLog::VISIBILITY_BOTH,
            'title' => 'Admin access granted',
            'description' => 'Admin User granted admin access to requester@example.com.',
        ]);

        $response = $this->actingAs($admin)->get(route('cabinet.admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Admin operations dashboard.');
        $response->assertSee('Total users');
        $response->assertSee('4');
        $response->assertSee('Admin users');
        $response->assertSee('3 standard users remain outside admin tools.');
        $response->assertSee('Pending access');
        $response->assertSee('Admin access requests waiting for review.');
        $response->assertSee('1 MFA');
        $response->assertSee('1 GitHub-connected users, 0 blocked logins.');
        $response->assertSee('GitHub identity');
        $response->assertSee('1 of 4');
        $response->assertSee('Application MFA');
        $response->assertSee('Login access');
        $response->assertSee('0 blocked');
        $response->assertSee('Review users');
        $response->assertSee(route('cabinet.admin.users'), false);
        $response->assertSee('Open content workspace');
        $response->assertSee(route('cabinet.admin.content'), false);
        $response->assertSee('Audit activity');
        $response->assertSee(route('cabinet.activity'), false);
        $response->assertSee('Recent admin activity');
        $response->assertSee('Admin access granted');
        $response->assertSee('Actor: Admin User');
        $response->assertSee('Subject: requester@example.com');
        $response->assertDontSee('Admin tools live inside the cabinet.');
        $response->assertDontSee('These screens are intentionally prepared');
    }
}
