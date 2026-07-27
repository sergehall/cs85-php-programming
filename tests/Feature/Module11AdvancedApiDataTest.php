<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class Module11AdvancedApiDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Cache::forget('module11a.json-placeholder.users.v1');
    }

    public function test_module_11_renders_the_advanced_track_with_live_api_data(): void
    {
        Http::fake([
            config('services.module11a.endpoint') => Http::response($this->payload()),
        ]);

        $response = $this->get(route('roadmap.module', 'module-11'));

        $response->assertOk();
        $response->assertSeeText('Module 11');
        $response->assertSeeText('Assignment 11A');
        $response->assertSeeText('Turn remote JSON into trusted application data.');
        $response->assertSeeText('Live data');
        $response->assertSeeText('Leanne Graham');
        $response->assertSeeText('How the API data reaches Blade');
        $response->assertSeeText('Four responsibilities, four clear owners');
        $response->assertSee(route('assignments.module11a.data'), false);

        Http::assertSentCount(1);
        Http::assertSent(
            fn ($request): bool => $request->url() === config('services.module11a.endpoint')
                && $request->hasHeader('Accept', 'application/json'),
        );
    }

    public function test_module_11_filters_and_sorts_normalized_contacts(): void
    {
        Http::fake([
            config('services.module11a.endpoint') => Http::response($this->payload()),
        ]);

        $this->get(route('assignments.module11a.index', [
            'search' => 'Gwenborough',
            'sort' => 'name_desc',
            'limit' => 4,
        ]))
            ->assertOk()
            ->assertSeeText('1 returned')
            ->assertSeeText('Leanne Graham')
            ->assertDontSeeText('Ervin Howell');
    }

    public function test_module_11_json_endpoint_exposes_the_trusted_contract(): void
    {
        Http::fake([
            config('services.module11a.endpoint') => Http::response($this->payload()),
        ]);

        $this->getJson(route('assignments.module11a.data', [
            'search' => 'Deckow',
            'limit' => 4,
        ]))
            ->assertOk()
            ->assertJsonPath('meta.source', 'live')
            ->assertJsonPath('meta.degraded', false)
            ->assertJsonPath('meta.returned', 1)
            ->assertJsonPath('data.0.name', 'Ervin Howell')
            ->assertJsonPath('data.0.company', 'Deckow-Crist')
            ->assertJsonMissingPath('data.0.address');
    }

    public function test_module_11_uses_the_versioned_fallback_when_the_api_fails(): void
    {
        Http::fake([
            config('services.module11a.endpoint') => Http::response([], 503),
        ]);

        $this->get(route('assignments.module11a.index'))
            ->assertOk()
            ->assertSeeText('Fallback data')
            ->assertSeeText('Fallback active')
            ->assertSeeText('Leanne Graham');
    }

    public function test_module_11_reuses_a_successful_cached_response(): void
    {
        Http::fake([
            config('services.module11a.endpoint') => Http::response($this->payload()),
        ]);

        $this->get(route('assignments.module11a.index'))->assertOk()->assertSeeText('Live data');
        $this->get(route('assignments.module11a.index'))->assertOk()->assertSeeText('Cache data');

        Http::assertSentCount(1);
    }

    public function test_module_11_rejects_unknown_query_controls(): void
    {
        Http::fake();

        $this->get(route('assignments.module11a.index', [
            'sort' => 'provider_sql',
            'limit' => 5000,
        ]))->assertSessionHasErrors(['sort', 'limit']);

        Http::assertNothingSent();
    }

    public function test_module_11_course_configuration_registers_the_advanced_view(): void
    {
        $module = collect(config('course.modules'))->firstWhere('slug', 'module-11');

        $this->assertIsArray($module);
        $this->assertSame('pages.assignments.module11.api-data', $module['view']);
        $this->assertFileExists(base_path('assignments/module11a/data/users.json'));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function payload(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Leanne Graham',
                'username' => 'Bret',
                'email' => 'Sincere@april.biz',
                'address' => ['city' => 'Gwenborough'],
                'phone' => '1-770-736-8031 x56442',
                'website' => 'hildegard.org',
                'company' => ['name' => 'Romaguera-Crona'],
            ],
            [
                'id' => 2,
                'name' => 'Ervin Howell',
                'username' => 'Antonette',
                'email' => 'Shanna@melissa.tv',
                'address' => ['city' => 'Wisokyburgh'],
                'phone' => '010-692-6593 x09125',
                'website' => 'anastasia.net',
                'company' => ['name' => 'Deckow-Crist'],
            ],
        ];
    }
}
