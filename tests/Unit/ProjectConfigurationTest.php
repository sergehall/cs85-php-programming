<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProjectConfigurationTest extends TestCase
{
    public function test_navigation_config_points_to_registered_routes(): void
    {
        foreach (['public', 'cabinet', 'cabinet_admin'] as $group) {
            foreach ($this->navigationGroup($group) as $item) {
                $this->assertTrue(
                    Route::has($item['route']),
                    "Navigation route [{$item['route']}] from group [{$group}] is not registered.",
                );
            }
        }
    }

    public function test_role_rules_prepare_user_and_admin_boundaries(): void
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
        $this->assertNotContains('manage_users', $roles['user']['abilities']);
    }

    public function test_course_roadmap_matches_six_week_summer_structure(): void
    {
        $modules = config('course.modules');

        $this->assertIsArray($modules);
        $this->assertCount(6, $modules);

        foreach ($modules as $module) {
            $this->assertIsArray($module);
            $this->assertNotEmpty($module['week']);
            $this->assertNotEmpty($module['title']);
            $this->assertNotEmpty($module['description']);
        }
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

    public function test_css_entrypoint_stays_tailwind_only(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString("@import 'tailwindcss';", $css);
        $this->assertStringNotContainsString('{', $css);
        $this->assertStringNotContainsString('}', $css);
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
}
