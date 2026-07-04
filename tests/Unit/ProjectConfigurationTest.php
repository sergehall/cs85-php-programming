<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProjectConfigurationTest extends TestCase
{
    public function test_navigation_config_points_to_registered_routes(): void
    {
        foreach ($this->navigationGroup('public') as $item) {
            $this->assertTrue(
                Route::has($item['route']),
                "Navigation route [{$item['route']}] from group [public] is not registered.",
            );
        }

        foreach (['user', 'admin'] as $group) {
            foreach ($this->cabinetNavigationGroup($group) as $item) {
                $this->assertTrue(
                    Route::has($item['route']),
                    "Cabinet route [{$item['route']}] from group [{$group}] is not registered.",
                );
            }
        }
    }

    public function test_role_rules_describe_real_user_and_admin_boundaries(): void
    {
        $roles = config('navigation.roles');

        $this->assertIsArray($roles);
        $this->assertArrayHasKey('user', $roles);
        $this->assertArrayHasKey('admin', $roles);

        foreach (['user', 'admin'] as $role) {
            $this->assertIsArray($roles[$role]);
            $this->assertNotEmpty($roles[$role]['label']);
            $this->assertNotEmpty($roles[$role]['description']);
            $this->assertIsArray($roles[$role]['abilities']);
            $this->assertContains('view_cabinet', $roles[$role]['abilities']);
        }

        $this->assertContains('manage_users', $roles['admin']['abilities']);
        $this->assertContains('review_admin_access_requests', $roles['admin']['abilities']);
        $this->assertContains('grant_admin_access', $roles['admin']['abilities']);
        $this->assertContains('revoke_admin_access', $roles['admin']['abilities']);
        $this->assertContains('view_admin_activity', $roles['admin']['abilities']);
        $this->assertContains('manage_own_profile', $roles['user']['abilities']);
        $this->assertContains('track_coursework', $roles['user']['abilities']);
        $this->assertContains('manage_own_security', $roles['user']['abilities']);
        $this->assertContains('use_application_mfa', $roles['user']['abilities']);
        $this->assertContains('request_admin_access', $roles['user']['abilities']);
        $this->assertContains('view_own_activity', $roles['user']['abilities']);
        $this->assertNotContains('manage_content', $roles['admin']['abilities']);
        $this->assertNotContains('review_messages', $roles['admin']['abilities']);
        $this->assertNotContains('send_messages', $roles['user']['abilities']);
        $this->assertNotContains('manage_users', $roles['user']['abilities']);
    }

    public function test_course_roadmap_prepares_twelve_clickable_modules(): void
    {
        $modules = config('course.modules');

        $this->assertIsArray($modules);
        $this->assertCount(12, $modules);

        foreach ($modules as $module) {
            $this->assertIsArray($module);
            $this->assertNotEmpty($module['module']);
            $this->assertNotEmpty($module['slug']);
            $this->assertNotEmpty($module['week']);
            $this->assertNotEmpty($module['week_number']);
            $this->assertNotEmpty($module['start']);
            $this->assertNotEmpty($module['title']);
            $this->assertNotEmpty($module['description']);
            $this->assertNotEmpty($module['status']);
            $this->assertNotEmpty($module['accent']);
            $this->assertIsArray($module['assignments']);
            foreach ($module['assignments'] as $assignment) {
                $this->assertIsArray($assignment);

                if (array_key_exists('route', $assignment)) {
                    $this->assertTrue(Route::has($assignment['route']));
                }
            }
            $this->assertIsArray($module['resources']);
            $this->assertIsArray($module['notes']);
            $this->assertTrue(Route::has('roadmap.module'));
        }

        $this->assertSame([1, 2, 3, 4, 5, 6], collect($modules)->pluck('week_number')->unique()->values()->all());
    }

    public function test_starter_stack_and_contact_data_are_ready_for_views(): void
    {
        $stack = config('course.stack');
        $contact = config('course.contact');

        $this->assertIsArray($stack);
        $this->assertNotEmpty($stack);

        foreach ($stack as $group) {
            $this->assertIsArray($group);
            $this->assertNotEmpty($group['category']);
            $this->assertIsArray($group['items']);
            $this->assertNotEmpty($group['items']);
        }

        $this->assertIsArray($contact);
        $this->assertNotEmpty($contact['email']);
        $this->assertIsArray($contact['profiles']);
        $this->assertNotEmpty($contact['profiles']);

        foreach ($contact['profiles'] as $profile) {
            $this->assertIsArray($profile);
            $this->assertNotEmpty($profile['label']);
            $this->assertNotEmpty($profile['href']);
        }
    }

    public function test_cabinet_configuration_is_ready_for_focused_pages(): void
    {
        $sections = config('cabinet.sections');
        $metrics = config('cabinet.metrics');
        $adminSections = config('cabinet.admin.sections');

        $this->assertIsArray($sections);
        $this->assertIsArray($metrics);
        $this->assertIsArray($adminSections);
        $this->assertArrayHasKey('profile', $sections);
        $this->assertArrayHasKey('coursework', $sections);
        $this->assertArrayHasKey('security', $sections);
        $this->assertArrayHasKey('activity', $sections);
        $this->assertArrayNotHasKey('messages', $sections);
        $this->assertArrayNotHasKey('messages', $adminSections);

        foreach ($sections as $section) {
            $this->assertIsArray($section);
            $this->assertNotEmpty($section['title']);
            $this->assertNotEmpty($section['description']);
            $this->assertIsArray($section['summary']);
            $this->assertIsArray($section['panels']);
            $this->assertIsArray($section['tasks']);
        }

        foreach ($metrics as $metric) {
            $this->assertIsArray($metric);
            $this->assertNotEmpty($metric['label']);
            $this->assertNotEmpty($metric['value']);
        }

        foreach ($adminSections as $section) {
            $this->assertIsArray($section);
            $this->assertNotEmpty($section['title']);
            $this->assertIsArray($section['items']);
            $this->assertIsArray($section['tasks']);
        }
    }

    public function test_cabinet_section_registries_drive_routes_and_navigation(): void
    {
        $sections = config('cabinet.sections');
        $adminSections = config('cabinet.admin.sections');

        $this->assertIsArray($sections);
        $this->assertIsArray($adminSections);

        foreach (array_keys($sections) as $sectionKey) {
            $this->assertTrue(Route::has("cabinet.{$sectionKey}"));
            $this->assertContains($sectionKey, $this->cabinetNavigationSectionKeys('user'));
        }

        foreach (array_keys($adminSections) as $sectionKey) {
            $this->assertTrue(Route::has("cabinet.admin.{$sectionKey}"));
            $this->assertContains($sectionKey, $this->cabinetNavigationSectionKeys('admin'));
        }

        $this->assertFalse(Route::has('cabinet.messages'));
        $this->assertFalse(Route::has('cabinet.admin.messages'));
    }

    public function test_css_entrypoint_stays_tailwind_only(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString("@import 'tailwindcss';", $css);
        $this->assertStringNotContainsString('{', $css);
        $this->assertStringNotContainsString('}', $css);
    }

    public function test_seo_assets_and_indexing_files_are_present(): void
    {
        $requiredPublicFiles = [
            'favicon.ico',
            'favicon-16x16.png',
            'favicon-32x32.png',
            'apple-touch-icon.png',
            'android-chrome-192x192.png',
            'android-chrome-512x512.png',
            'og-image.png',
            'manifest.webmanifest',
            'robots.txt',
            'sitemap.xml',
            'assets/brand/cs85-logo.png',
            'assets/brand/cs85-logo-192.png',
            'assets/brand/cs85-logo-512.png',
        ];

        foreach ($requiredPublicFiles as $file) {
            $path = public_path($file);

            $this->assertFileExists($path);
            $this->assertGreaterThan(0, filesize($path));
        }

        $robots = file_get_contents(public_path('robots.txt'));
        $sitemap = file_get_contents(public_path('sitemap.xml'));

        $this->assertIsString($robots);
        $this->assertIsString($sitemap);
        $this->assertStringContainsString('Disallow: /cabinet', $robots);
        $this->assertStringContainsString('Sitemap: http://127.0.0.1:8000/sitemap.xml', $robots);
        $this->assertStringContainsString('<loc>http://127.0.0.1:8000/roadmap</loc>', $sitemap);
        $this->assertStringNotContainsString('/cabinet', $sitemap);
        $this->assertStringNotContainsString('/login', $sitemap);
    }

    public function test_home_page_exposes_brand_and_seo_metadata(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('rel="manifest"', false);
        $response->assertSee('favicon-32x32.png', false);
        $response->assertSee('apple-touch-icon.png', false);
        $response->assertSee('og:image', false);
        $response->assertSee('summary_large_image', false);
        $response->assertSee('assets/brand/cs85-logo-192.png', false);
        $response->assertSee('index, follow, max-image-preview:large', false);
    }

    /**
     * @return list<array{label: string, route: string}>
     */
    private function navigationGroup(string $key): array
    {
        $items = config("navigation.{$key}");

        $this->assertIsArray($items);

        $validatedItems = [];

        foreach ($items as $item) {
            if (
                ! is_array($item) ||
                ! isset($item['label'], $item['route']) ||
                ! is_string($item['label']) ||
                ! is_string($item['route'])
            ) {
                $this->fail("Navigation group [{$key}] contains an invalid item.");
            }

            $validatedItems[] = [
                'label' => $item['label'],
                'route' => $item['route'],
            ];
        }

        return $validatedItems;
    }

    /**
     * @return list<array{label: string, route: string, section?: string}>
     */
    private function cabinetNavigationGroup(string $key): array
    {
        $items = config("cabinet.navigation.{$key}");

        $this->assertIsArray($items);

        $validatedItems = [];

        foreach ($items as $item) {
            if (
                ! is_array($item) ||
                ! isset($item['label'], $item['route']) ||
                ! is_string($item['label']) ||
                ! is_string($item['route'])
            ) {
                $this->fail("Cabinet navigation group [{$key}] contains an invalid item.");
            }

            $validatedItem = [
                'label' => $item['label'],
                'route' => $item['route'],
            ];

            if (isset($item['section']) && is_string($item['section'])) {
                $validatedItem['section'] = $item['section'];
            }

            $validatedItems[] = $validatedItem;
        }

        return $validatedItems;
    }

    /**
     * @return list<string>
     */
    private function cabinetNavigationSectionKeys(string $key): array
    {
        $sectionKeys = [];

        foreach ($this->cabinetNavigationGroup($key) as $item) {
            if (isset($item['section'])) {
                $sectionKeys[] = $item['section'];
            }
        }

        return $sectionKeys;
    }
}
