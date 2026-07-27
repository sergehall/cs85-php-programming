<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class Module11WeatherAssignmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_course_aligned_weather_page_decodes_the_required_static_json(): void
    {
        $response = $this->get(route('assignments.module11a.weather'));

        $response->assertOk();
        $response->assertSeeText('Assignment 11A');
        $response->assertSeeText('Course-aligned track');
        $response->assertSeeText('Weekly Weather Forecast');
        $response->assertSeeText('Monday');
        $response->assertSeeText('75°F');
        $response->assertSeeText('Sunny');
        $response->assertSeeText('Tuesday');
        $response->assertSeeText('Partly Cloudy');
        $response->assertSeeText('Wednesday');
        $response->assertSeeText('Rain');
        $response->assertSeeText('Everything the grader should find');
        $response->assertSee(route('roadmap.module', 'module-11'), false);
    }

    public function test_course_aligned_weather_page_supports_optional_alphabetical_sorting(): void
    {
        $response = $this->get(route('assignments.module11a.weather', ['sort' => 'alpha']));

        $response->assertOk();
        $content = $response->getContent();

        $this->assertIsString($content);
        preg_match('/<tbody[^>]*>(.*?)<\/tbody>/s', $content, $matches);
        $tableBody = $matches[1] ?? '';

        $this->assertNotSame('', $tableBody);
        $this->assertLessThan(
            strpos($tableBody, 'Tuesday'),
            strpos($tableBody, 'Monday'),
            'Monday should render before Tuesday in alphabetical mode.',
        );
        $this->assertLessThan(
            strpos($tableBody, 'Wednesday'),
            strpos($tableBody, 'Tuesday'),
            'Tuesday should render before Wednesday in alphabetical mode.',
        );
    }

    public function test_course_aligned_files_show_the_required_storage_decode_and_blade_flow(): void
    {
        $this->assertFileExists(storage_path('app/private/weather.json'));
        $this->assertFileExists(app_path('Http/Controllers/WeatherController.php'));
        $this->assertFileExists(resource_path('views/weather/index.blade.php'));

        $controller = file_get_contents(app_path('Http/Controllers/WeatherController.php'));
        $view = file_get_contents(resource_path('views/weather/index.blade.php'));

        $this->assertIsString($controller);
        $this->assertIsString($view);
        $this->assertStringContainsString("Storage::get('weather.json')", $controller);
        $this->assertStringContainsString('json_decode($json, true', $controller);
        $this->assertStringContainsString("view('weather.index'", $controller);
        $this->assertStringContainsString('@foreach ($weather as $day)', $view);
        $this->assertStringContainsString("{{ \$day['high'] }}°F", $view);
    }

    public function test_module_11_course_configuration_registers_assignment_11a(): void
    {
        $module = collect(config('course.modules'))->firstWhere('slug', 'module-11');

        $this->assertIsArray($module);
        $this->assertSame('Complete', $module['status']);
        $this->assertCount(1, $module['assignments']);
        $this->assertSame('Assignment 11A', $module['assignments'][0]['label']);
        $this->assertSame('API Data', $module['assignments'][0]['title']);
        $this->assertSame('Complete', $module['assignments'][0]['status']);
        $this->assertSame('assignments.module11a.weather', $module['assignments'][0]['route']);
        $this->assertSame('assignments/module11a/README.md', $module['assignments'][0]['source']);
        $this->assertFileExists(storage_path('app/private/weather.json'));
    }
}
