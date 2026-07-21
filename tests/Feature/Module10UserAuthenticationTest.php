<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Module10UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_module_10_renders_the_advanced_authentication_track(): void
    {
        $response = $this->get(route('roadmap.module', 'module-10'));

        $response->assertOk();
        $response->assertSeeText('Module 10');
        $response->assertSeeText('Advanced track');
        $response->assertSeeText('A user-aware Laravel application.');
        $response->assertSeeText('Production behavior, delivered.');
        $response->assertSeeText('User-aware behavior');
        $response->assertSeeText('Every advanced behavior points to runtime code and an automated feature-test owner.');
        $response->assertSeeText('How a user reaches the cabinet');
        $response->assertSeeText('The four main authentication nodes');
        $response->assertSeeText('Authentication is a flow, not a single form.');
        $response->assertSee(route('assignments.module10a.overview'), false);
        $response->assertSee(route('register'), false);
        $response->assertSee(route('login'), false);
        $response->assertSee(route('cabinet.security'), false);
    }

    public function test_module_10_renders_the_course_aligned_assignment_track(): void
    {
        $response = $this->get(route('assignments.module10a.overview'));

        $response->assertOk();
        $response->assertSeeText('Assignment 10A');
        $response->assertSeeText('Course-aligned track');
        $response->assertSeeText('Run the required flow');
        $response->assertSeeText('Everything the grader should find');
        $response->assertSeeText('Concept-to-code map');
        $response->assertSee(route('dashboard'), false);
        $response->assertSee(route('secret'), false);
        $response->assertSee(route('roadmap.module', 'module-10'), false);
    }

    public function test_module_10_configuration_marks_assignment_10a_complete(): void
    {
        $module = collect(config('course.modules'))->firstWhere('slug', 'module-10');

        $this->assertIsArray($module);
        $this->assertSame('Complete', $module['status']);
        $this->assertSame('pages.assignments.module10.user-authentication', $module['view']);
        $this->assertCount(1, $module['assignments']);
        $this->assertSame('Assignment 10A', $module['assignments'][0]['label']);
        $this->assertSame('Complete', $module['assignments'][0]['status']);
        $this->assertSame('/roadmap/module-10/assignment', $module['assignments'][0]['url']);
        $this->assertSame('assignments/module10a/README.md', $module['assignments'][0]['source']);
    }

    public function test_assignment_files_include_the_required_code_and_written_answers(): void
    {
        $this->assertFileExists(base_path('routes/auth.php'));
        $this->assertFileExists(base_path('assignments/module10a/routes/web.php'));
        $this->assertFileExists(base_path('assignments/module10a/resources/views/dashboard.blade.php'));
        $this->assertFileExists(base_path('assignments/module10a/resources/views/secret.blade.php'));
        $this->assertFileExists(base_path('assignments/module10a/docs/screenshots/README.md'));

        $readme = file_get_contents(base_path('assignments/module10a/README.md'));

        $this->assertIsString($readme);
        $this->assertStringContainsString('What is the difference between authentication and authorization?', $readme);
        $this->assertStringContainsString('Why are passwords hashed instead of stored as plain text?', $readme);
        $this->assertStringContainsString('Which file defines the `/login` and `/register` routes?', $readme);
        $this->assertStringContainsString('What does the `auth` middleware do?', $readme);
    }

    public function test_assignment_login_creates_an_authenticated_session(): void
    {
        $user = User::factory()->create([
            'email' => 'module10@example.com',
            'password' => 'StrongPassword123!',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'module10@example.com',
            'password' => 'StrongPassword123!',
        ]);

        $response->assertRedirect(route('cabinet.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_assignment_dashboard_redirects_guests_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_assignment_dashboard_greets_the_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Module Ten Student',
            'email' => 'student10@example.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Welcome back, Module Ten Student!');
        $response->assertSeeText('student10@example.com');
        $response->assertSee(route('secret'), false);
    }

    public function test_assignment_secret_page_is_protected(): void
    {
        $this->get(route('secret'))->assertRedirect(route('login'));

        $user = User::factory()->create(['name' => 'Secret Member']);
        $response = $this->actingAs($user)->get(route('secret'));

        $response->assertOk();
        $response->assertSeeText('Members only!');
        $response->assertSeeText('Secret Member');
    }

    public function test_assignment_enforces_user_and_admin_roles(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get(route('cabinet.admin.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('cabinet.admin.dashboard'))->assertOk();
    }

    public function test_assignment_logout_removes_authentication_state(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
