<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SiteNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    #[DataProvider('publicRoutes')]
    public function test_public_pages_render_successfully(string $path, string $expectedText): void
    {
        $response = $this->get($path);

        $response->assertOk();
        $response->assertSee($expectedText);
        $response->assertDontSee('A minimal Laravel project');
    }

    public function test_home_page_exposes_expandable_navigation(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Roadmap');
        $response->assertSee('Stack');
        $response->assertSee('Contact');
        $response->assertSee('Cabinet');
        $response->assertDontSee('Open admin foundation');
    }

    public function test_contact_page_keeps_email_protected_until_user_action(): void
    {
        $response = $this->get('/contact');

        $response->assertOk();
        $response->assertSee('Protected email');
        $response->assertSee('Show email');
        $response->assertDontSee(config('course.contact.email'), false);
        $response->assertDontSee('mailto:', false);
    }

    public function test_legacy_admin_entry_redirects_to_cabinet(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/cabinet');
    }

    public function test_roadmap_links_to_each_module_page(): void
    {
        $response = $this->get('/roadmap');

        $response->assertOk();
        $response->assertSee('CS85 modules by week');
        $response->assertSee('Course Schedule');

        foreach (config('course.modules') as $module) {
            $response->assertSee(route('roadmap.module', $module['slug']), false);
        }
    }

    public function test_roadmap_module_pages_render_placeholder_workspace(): void
    {
        $module = config('course.modules.0');

        $response = $this->get(route('roadmap.module', $module['slug']));

        $response->assertOk();
        $response->assertSee('Roadmap module switcher');
        $response->assertSee('aria-current="page"', false);
        $response->assertSee($module['title']);
        $response->assertSee('Assignments');
        $response->assertSee('Notes');
        $response->assertSee('Resources');
        $response->assertSee('Laravel Hello World');
        $response->assertSee(route('assignments.module1.assignment1a'), false);
        $this->assertMatchesRegularExpression(
            '/href="'.preg_quote(route('roadmap'), '/').'"\s+class="[^"]*bg-white text-slate-950[^"]*"/',
            $response->getContent()
        );

        foreach (config('course.modules') as $roadmapModule) {
            $response->assertSee(route('roadmap.module', $roadmapModule['slug']), false);
        }
    }

    public function test_completed_assignment_pages_render_successfully(): void
    {
        $this->get(route('assignments.module1.assignment1a'))
            ->assertOk()
            ->assertSee('Hello World from Laravel Herd!');

        $this->get(route('assignments.module2.cosmic-calendar'))
            ->assertOk()
            ->assertSee('Cosmic Day Number Calendar')
            ->assertSee('First Name');

        $this->assertFileExists(base_path('assignments/module2a/price_engine.php'));
        $this->assertFileExists(base_path('assignments/module2a/price_engine_refactored.php'));

        $this->get('/assignments/module2a/price_engine.php')
            ->assertOk()
            ->assertSee('Order Summary');

        $this->get('/assignments/module2a/price_engine_refactored.php')
            ->assertOk()
            ->assertSee('T-Shirt Price Engine Refactored');

        $this->assertFileExists(base_path('assignments/module5a/MyObject.php'));
        $this->assertFileExists(base_path('assignments/module5a/critique.md'));

        $this->get('/assignments/module5a/MyObject.php')
            ->assertOk()
            ->assertSee('Designing Your Own Object Oriented World')
            ->assertSee('Task Summary:')
            ->assertSee('AI Method Critique');

        $this->assertFileExists(base_path('assignments/module6a/public/index.php'));
        $this->assertFileExists(base_path('assignments/module6a/src/Models/PhotographyProject.php'));
        $this->assertFileExists(base_path('assignments/module6a/src/Controllers/BookingPlannerController.php'));
        $this->assertFileExists(base_path('assignments/module6a/views/booking-planner.php'));
        $this->assertFileExists(base_path('assignments/module6a/README.md'));

        $this->get('/assignments/module6a/public/index.php')
            ->assertOk()
            ->assertSee('MVC-Based PHP Application')
            ->assertSee('PhotographyProject')
            ->assertSee('BookingPlannerController')
            ->assertSee('Quote total');
    }

    public function test_module6a_mvc_assignment_validates_posted_input(): void
    {
        $response = $this->post('/assignments/module6a/public/index.php', [
            'client_name' => 'A',
            'service_type' => 'fashion',
            'package' => 'premium',
            'hours' => '12',
            'edited_photos' => '100',
            'location_type' => 'out_of_city',
            'deposit_paid' => '-10',
            'project_note' => '',
            'rush_delivery' => '1',
        ]);

        $response->assertOk();
        $response->assertSee('Validation adjusted the request');
        $response->assertSee('Client name must be at least 2 characters.');
        $response->assertSee('Session hours must be between 1 and 8.');
        $response->assertSee('Edited photos must be between 5 and 80.');
        $response->assertSee('Deposit paid must be between 0 and 5000.');
        $response->assertSee('Project note is required.');
    }

    public function test_unknown_roadmap_module_returns_not_found(): void
    {
        $response = $this->get('/roadmap/not-a-real-module');

        $response->assertNotFound();
    }

    public function test_guest_is_redirected_from_cabinet_to_login(): void
    {
        $response = $this->get('/cabinet');

        $response->assertRedirect('/login');
    }

    #[DataProvider('cabinetRoutes')]
    public function test_cabinet_pages_render_successfully(string $path, string $expectedText): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($path);

        $response->assertOk();
        $response->assertSee('Cabinet');
        $response->assertSee($expectedText);
    }

    public function test_cabinet_coursework_links_to_real_assignment_architecture(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/cabinet/coursework');

        $response->assertOk();
        $response->assertSee('Coursework Control Center');
        $response->assertSee('Source root');
        $response->assertSee('assignments/');
        $response->assertSee('Laravel Hello World');
        $response->assertSee(route('assignments.module1.assignment1a'), false);
        $response->assertSee(url('/assignments/module2a/price_engine.php'), false);
        $response->assertSee(url('/assignments/module4b/show_inventory.php'), false);
        $response->assertSee('Open assignment');
        $response->assertDontSee('<code', false);
        $response->assertDontSee('block font-bold text-slate-500">Source</span>', false);
        $response->assertDontSee('resources/views/pages/assignments/module1/assignment1a.blade.php');
        $response->assertDontSee('Create assignment records');
    }

    public function test_cabinet_dashboard_shows_current_overview_controls(): void
    {
        $user = User::factory()->create([
            'name' => 'Serge Hall',
            'profile_photo_url' => 'https://cdn.example.com/profile.jpg',
            'github_id' => '12345',
        ]);

        $response = $this->actingAs($user)->get('/cabinet');

        $response->assertOk();
        $response->assertSee('Overview for your CS85 Laravel workspace.');
        $response->assertSee('profile identity, coursework links, account security');
        $response->assertSee('activity evidence');
        $response->assertSee('https://cdn.example.com/profile.jpg', false);
        $response->assertSee('GitHub connected');
        $response->assertSee('Tracked Areas');
        $response->assertSee('Security Controls');
        $response->assertSee('custom photo URL');
        $response->assertSee('app MFA');
        $response->assertSee('five-at-a-time browsing');
        $response->assertSee('Application MFA is available');
        $response->assertSee('Access roles');
        $response->assertSee('Configured role');
        $response->assertSee('Standard student workspace role');
        $response->assertSee('Connect GitHub, enable application MFA, and request admin access');
        $response->assertSee('Protected administrative role');
        $response->assertSee('Allow or block user login access');
        $response->assertSee('coursework remains in the shared coursework page');
        $response->assertDontSee('request_admin_access');
        $response->assertDontSee('grant_admin_access');
        $response->assertDontSee('view_admin_activity');
        $response->assertDontSee('MFA planning');
        $response->assertDontSee('30+');
        $response->assertDontSee('Prepared access roles');
        $response->assertDontSee('Prepared role');
    }

    public function test_cabinet_security_page_shows_real_security_controls(): void
    {
        config([
            'services.github.client_id' => 'client-id',
            'services.github.client_secret' => 'client-secret',
        ]);

        $user = User::factory()->create([
            'github_id' => '12345',
            'github_username' => 'sergehall',
        ]);

        $response = $this->actingAs($user)->get('/cabinet/security');

        $response->assertOk();
        $response->assertSee('Security Foundation');
        $response->assertSee('GitHub identity');
        $response->assertSee('Reconnect GitHub');
        $response->assertSee('sergehall');
        $response->assertSee('Session authentication');
        $response->assertSee('GitHub OAuth configuration');
        $response->assertSee('Application MFA');
        $response->assertSee('Admin access');
        $response->assertSee('Request admin access');
        $response->assertSee('Current Laravel account:');
        $response->assertSee('GitHub does not look up this email automatically.');
        $response->assertSee('A GitHub identity can be connected to only one CS85 user at a time.');
        $response->assertSee('For privacy, CS85 does not reveal whether a GitHub identity is connected to another profile.');
        $response->assertDontSee('Security roadmap');
        $response->assertDontSee('GitHub MFA');
        $response->assertDontSee('Managed in GitHub');
    }

    #[DataProvider('cabinetAdminRoutes')]
    public function test_admin_rule_pages_render_inside_cabinet(string $path, string $expectedText): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get($path);

        $response->assertOk();
        $response->assertSee('Admin');
        $response->assertSee($expectedText);
    }

    public function test_user_cannot_open_admin_cabinet_rules(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/cabinet/admin');

        $response->assertForbidden();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function publicRoutes(): array
    {
        return [
            'home' => ['/', 'CS85 PHP Programming'],
            'roadmap' => ['/roadmap', 'Course Roadmap'],
            'stack' => ['/stack', 'Starter Stack'],
            'contact' => ['/contact', 'Protected email'],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function cabinetRoutes(): array
    {
        return [
            'dashboard' => ['/cabinet', 'Overview for your CS85 Laravel workspace'],
            'profile' => ['/cabinet/profile', 'Profile'],
            'coursework' => ['/cabinet/coursework', 'Coursework'],
            'security' => ['/cabinet/security', 'Security Foundation'],
            'activity' => ['/cabinet/activity', 'Activity Timeline'],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function cabinetAdminRoutes(): array
    {
        return [
            'dashboard' => ['/cabinet/admin', 'Admin operations dashboard'],
            'users' => ['/cabinet/admin/users', 'Users'],
        ];
    }
}
