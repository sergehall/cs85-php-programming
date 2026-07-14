<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\AdminAccessRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTimelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_profile_update_creates_user_activity(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('cabinet.profile.update'), [
            'first_name' => 'Serge',
            'last_name' => 'Hall',
            'github_profile_url' => 'https://github.com/sergehall',
            'linkedin_profile_url' => 'https://www.linkedin.com/in/sergehall',
            'bio' => 'CS85 student building a Laravel cabinet.',
            'technical_skills' => 'PHP, Laravel, MySQL',
        ])->assertRedirect(route('cabinet.profile'));

        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'actor_user_id' => $user->id,
            'category' => 'profile',
            'event' => 'profile.updated',
            'title' => 'Profile updated',
        ]);

        $this->actingAs($user)
            ->get(route('cabinet.activity'))
            ->assertOk()
            ->assertSee('My activity')
            ->assertSee('Profile updated')
            ->assertDontSee('Administrative timeline');
    }

    public function test_coursework_page_creates_one_daily_user_activity(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('cabinet.coursework'))->assertOk();
        $this->actingAs($user)->get(route('cabinet.coursework'))->assertOk();

        $this->assertSame(
            1,
            ActivityLog::query()
                ->where('subject_user_id', $user->id)
                ->where('event', 'coursework.workspace_viewed')
                ->count(),
        );

        $this->actingAs($user)
            ->get(route('cabinet.activity'))
            ->assertOk()
            ->assertSee('Coursework workspace opened');
    }

    public function test_admin_access_request_is_visible_to_user_and_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('cabinet.security.admin-access-request'))
            ->assertRedirect(route('cabinet.security'));

        $this->actingAs($user)
            ->get(route('cabinet.activity'))
            ->assertOk()
            ->assertSee('Admin access requested')
            ->assertDontSee('Administrative timeline');

        $this->actingAs($admin)
            ->get(route('cabinet.activity'))
            ->assertOk()
            ->assertSee('Administrative timeline')
            ->assertSee('Admin access requested')
            ->assertSee($user->email);
    }

    public function test_admin_grant_and_revoke_are_recorded_for_user_and_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $accessRequest = AdminAccessRequest::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin)
            ->withSecurityConfirmation($admin)
            ->patch(route('cabinet.admin.access-requests.approve', $accessRequest))
            ->assertRedirect(route('cabinet.admin.users'));

        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'actor_user_id' => $admin->id,
            'category' => 'admin',
            'event' => 'admin_access.granted',
            'visibility' => ActivityLog::VISIBILITY_BOTH,
        ]);

        $this->actingAs($admin)
            ->withSecurityConfirmation($admin)
            ->patch(route('cabinet.admin.users.revoke-admin', $user))
            ->assertRedirect(route('cabinet.admin.users'));

        $this->assertDatabaseHas('activity_logs', [
            'subject_user_id' => $user->id,
            'actor_user_id' => $admin->id,
            'category' => 'admin',
            'event' => 'admin_access.revoked',
            'visibility' => ActivityLog::VISIBILITY_BOTH,
        ]);

        $this->actingAs($user)
            ->get(route('cabinet.activity'))
            ->assertOk()
            ->assertSee('Admin access granted')
            ->assertSee('Admin access revoked')
            ->assertSee('Actor: '.$admin->name);

        $this->actingAs($admin)
            ->get(route('cabinet.activity'))
            ->assertOk()
            ->assertSee('Administrative timeline')
            ->assertSee('Admin access granted')
            ->assertSee('Admin access revoked');
    }

    public function test_user_activity_is_paginated_in_groups_of_five(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 7) as $index) {
            ActivityLog::factory()->create([
                'subject_user_id' => $user->id,
                'actor_user_id' => $user->id,
                'category' => 'security',
                'event' => "security.event_{$index}",
                'visibility' => ActivityLog::VISIBILITY_USER,
                'title' => "Activity event {$index}",
                'description' => "Activity description {$index}",
                'created_at' => now()->subMinutes(7 - $index),
            ]);
        }

        $this->actingAs($user)
            ->get(route('cabinet.activity'))
            ->assertOk()
            ->assertSee('Showing 1-5 of 7')
            ->assertSee('Show next 5')
            ->assertSee('Activity event 7')
            ->assertSee('Activity event 3')
            ->assertDontSee('Activity event 2')
            ->assertDontSee('Activity event 1');

        $this->actingAs($user)
            ->get(route('cabinet.activity', ['my_activity_page' => 2]))
            ->assertOk()
            ->assertSee('Showing 6-7 of 7')
            ->assertSee('Previous 5')
            ->assertSee('All activity shown')
            ->assertSee('Activity event 2')
            ->assertSee('Activity event 1')
            ->assertDontSee('Activity event 3');
    }
}
