<?php

declare(strict_types=1);

namespace App\Http\Controllers\Assignments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assignments\BrowseModule11ApiDataRequest;
use App\Services\Modules\Module11A\Application\ApiContactResult;
use App\Services\Modules\Module11A\Application\BrowseApiContacts;
use App\Services\Modules\Module11A\Domain\ApiContact;
use App\Services\Modules\Module11A\Infrastructure\JsonPlaceholderUserClient;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

final class Module11AApiDataController extends Controller
{
    public function __invoke(
        BrowseModule11ApiDataRequest $request,
        JsonPlaceholderUserClient $client,
        BrowseApiContacts $browser,
    ): View {
        $page = $this->buildPage($request, $client, $browser);
        $modules = collect(config('course.modules'));
        $module = $modules->firstWhere('slug', 'module-11');

        abort_unless(is_array($module), 404);

        return view('pages.assignments.module11.api-data', [
            ...$page,
            'module' => $module,
            'modules' => $modules,
            'position' => $modules->search(
                static fn (array $item): bool => $item['slug'] === 'module-11',
            ) + 1,
        ]);
    }

    public function data(
        BrowseModule11ApiDataRequest $request,
        JsonPlaceholderUserClient $client,
        BrowseApiContacts $browser,
    ): JsonResponse {
        $page = $this->buildPage($request, $client, $browser);

        return response()->json([
            'meta' => [
                'source' => $page['apiResult']->source,
                'degraded' => $page['apiResult']->degraded,
                'fetched_at' => $page['apiResult']->fetchedAt,
                'total' => $page['dataset']['total'],
                'returned' => $page['dataset']['returned'],
                'filters' => $page['filters'],
            ],
            'data' => array_map(
                static fn (ApiContact $contact): array => $contact->toArray(),
                $page['dataset']['contacts'],
            ),
        ]);
    }

    /**
     * @return array{
     *     apiResult: ApiContactResult,
     *     dataset: array{contacts: list<ApiContact>, total: int, returned: int, companies: int, cities: int},
     *     filters: array{search: string, sort: string, limit: int},
     *     sorts: array<string, string>
     * }
     */
    private function buildPage(
        BrowseModule11ApiDataRequest $request,
        JsonPlaceholderUserClient $client,
        BrowseApiContacts $browser,
    ): array {
        $validated = $request->validated();
        $filters = [
            'search' => (string) ($validated['search'] ?? ''),
            'sort' => (string) ($validated['sort'] ?? 'name_asc'),
            'limit' => (int) ($validated['limit'] ?? 10),
        ];
        $apiResult = $client->fetch($request->boolean('refresh'));

        return [
            'apiResult' => $apiResult,
            'dataset' => $browser->handle(
                $apiResult,
                $filters['search'],
                $filters['sort'],
                $filters['limit'],
            ),
            'filters' => $filters,
            'sorts' => BrowseApiContacts::SORTS,
        ];
    }
}
