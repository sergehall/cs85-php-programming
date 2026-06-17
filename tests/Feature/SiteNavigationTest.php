<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SiteNavigationTest extends TestCase
{
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

    public function test_legacy_admin_entry_redirects_to_cabinet(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/cabinet');
    }

    #[DataProvider('cabinetRoutes')]
    public function test_cabinet_pages_render_successfully(string $path, string $expectedText): void
    {
        $response = $this->get($path);

        $response->assertOk();
        $response->assertSee('Cabinet');
        $response->assertSee($expectedText);
    }

    #[DataProvider('cabinetAdminRoutes')]
    public function test_admin_rule_pages_render_inside_cabinet(string $path, string $expectedText): void
    {
        $response = $this->get($path);

        $response->assertOk();
        $response->assertSee('Admin');
        $response->assertSee($expectedText);
    }

    public static function publicRoutes(): array
    {
        return [
            'home' => ['/', 'CS85 PHP Programming'],
            'roadmap' => ['/roadmap', 'Course Roadmap'],
            'stack' => ['/stack', 'Starter Stack'],
            'contact' => ['/contact', 'serge.hall.dev@gmail.com'],
            'cabinet' => ['/cabinet', 'Cabinet Foundation'],
        ];
    }

    public static function cabinetRoutes(): array
    {
        return [
            'dashboard' => ['/cabinet', 'Personal workspace first'],
            'profile' => ['/cabinet/profile', 'Profile'],
            'coursework' => ['/cabinet/coursework', 'Coursework'],
            'messages' => ['/cabinet/messages', 'Messages'],
        ];
    }

    public static function cabinetAdminRoutes(): array
    {
        return [
            'dashboard' => ['/cabinet/admin', 'Admin tools live inside the cabinet'],
            'users' => ['/cabinet/admin/users', 'Users'],
            'content' => ['/cabinet/admin/content', 'Content'],
            'messages' => ['/cabinet/admin/messages', 'Messages'],
        ];
    }
}
