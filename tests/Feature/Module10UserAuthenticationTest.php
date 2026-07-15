<?php

namespace Tests\Feature;

use Tests\TestCase;

class Module10UserAuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_module_10_renders_the_user_authentication_assignment(): void
    {
        $response = $this->get(route('roadmap.module', 'module-10'));

        $response->assertOk();
        $response->assertSeeText('Module 10');
        $response->assertSeeText('Assignment 10A');
        $response->assertSeeText('A user-aware Laravel application.');
        $response->assertSeeText('Required behavior, delivered.');
        $response->assertSeeText('How a user reaches the cabinet');
        $response->assertSeeText('The four main authentication nodes');
        $response->assertSeeText('Authentication is a flow, not a single form.');
        $response->assertSee(route('register'), false);
        $response->assertSee(route('login'), false);
        $response->assertSee(route('cabinet.security'), false);
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
    }
}
