<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_assignment_page_explains_the_routes(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Module 7 Assignment 7A');
        $response->assertSee('Hello Route');
        $response->assertSee('/hello');
        $response->assertSee('/greet/{name}');
        $response->assertSee('serge-hall');
        $response->assertSee('Vicky Seno');
        $response->assertSee('button button-instructor', false);
        $response->assertDontSee('Try Samantha');
    }

    public function test_hello_route_returns_the_required_message(): void
    {
        $this->get('/hello')
            ->assertOk()
            ->assertSeeText('Hello from Laravel!');
    }

    public function test_greet_route_capitalizes_the_name(): void
    {
        $this->get('/greet/alex')
            ->assertOk()
            ->assertSeeText('Hello, Alex!');

        $this->get('/greet/samantha')
            ->assertOk()
            ->assertSeeText('Hello, Samantha!');

        $this->get('/greet/serge-hall')
            ->assertOk()
            ->assertSeeText('Hello, Serge Hall!');
    }

    public function test_instructor_greeting_uses_the_presentation_view(): void
    {
        $this->get('/greet/vicky-seno')
            ->assertOk()
            ->assertSee('Instructor Spotlight')
            ->assertSee('Vicky Seno')
            ->assertSeeText('Hello, Vicky Seno!')
            ->assertSee('prefers-reduced-motion');
    }

    public function test_greet_route_rejects_invalid_name_characters(): void
    {
        $this->get('/greet/%3Cscript%3E')
            ->assertNotFound();

        $this->get('/greet/serge--hall')
            ->assertNotFound();

        $this->get('/greet/serge%20hall')
            ->assertNotFound();
    }
}
