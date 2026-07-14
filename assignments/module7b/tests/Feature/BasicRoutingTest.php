<?php

namespace Tests\Feature;

use App\Http\Controllers\HobbyController;
use Closure;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BasicRoutingTest extends TestCase
{
    public function test_home_closure_passes_personal_data_to_the_view(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Welcome to My Personal Route Lab')
            ->assertSee('Siarhei Hancharou')
            ->assertSee('aria-current="page"', false);
    }

    public function test_about_closure_passes_student_data_to_the_view(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('About me')
            ->assertSee('Prefer not to disclose')
            ->assertSee('Santa Monica College')
            ->assertSee('Web Development (A.S.)')
            ->assertSee('webdev-coursework.com')
            ->assertSee('lens-lounge.com')
            ->assertSee('sergioartg.com')
            ->assertSee('github.com/SergeHall')
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_hobby_index_is_rendered_by_the_controller(): void
    {
        $this->get('/hobbies')
            ->assertOk()
            ->assertSee('My hobbies')
            ->assertSee('Photography')
            ->assertSee('Web Development')
            ->assertSee('Technology Projects')
            ->assertSee('aria-current="page"', false);
    }

    #[DataProvider('hobbyPages')]
    public function test_dynamic_hobby_route_displays_the_selected_record(int $id, string $name): void
    {
        $this->get("/hobbies/{$id}")
            ->assertOk()
            ->assertSee($name)
            ->assertSee("/hobbies/{$id}")
            ->assertSee('hobbies.show')
            ->assertSee('aria-current="page"', false);
    }

    public function test_unknown_hobby_returns_not_found(): void
    {
        $this->get('/hobbies/999')->assertNotFound();
    }

    public function test_non_numeric_hobby_id_does_not_match_the_route(): void
    {
        $this->get('/hobbies/not-a-number')->assertNotFound();
    }

    public function test_named_routes_use_the_required_closure_and_controller_approaches(): void
    {
        $homeRoute = Route::getRoutes()->getByName('home');
        $aboutRoute = Route::getRoutes()->getByName('about');
        $indexRoute = Route::getRoutes()->getByName('hobbies.index');
        $showRoute = Route::getRoutes()->getByName('hobbies.show');

        $this->assertNotNull($homeRoute);
        $this->assertNotNull($aboutRoute);
        $this->assertNotNull($indexRoute);
        $this->assertNotNull($showRoute);
        $this->assertInstanceOf(Closure::class, $homeRoute->getAction('uses'));
        $this->assertInstanceOf(Closure::class, $aboutRoute->getAction('uses'));
        $this->assertSame(HobbyController::class.'@index', $indexRoute->getActionName());
        $this->assertSame(HobbyController::class.'@show', $showRoute->getActionName());
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function hobbyPages(): array
    {
        return [
            'photography' => [1, 'Photography'],
            'web development' => [2, 'Web Development'],
            'technology projects' => [3, 'Technology Projects'],
        ];
    }
}
