<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class CourseworkController extends Controller
{
    public function __invoke(): View
    {
        $modules = collect(config('course.modules', []))
            ->map(fn (array $module): array => $this->prepareModule($module))
            ->all();

        $assignments = collect($modules)
            ->flatMap(fn (array $module): array => $module['assignments'])
            ->all();

        return view('cabinet.coursework', [
            'section' => config('cabinet.sections.coursework'),
            'modules' => $modules,
            'assignmentCount' => count($assignments),
            'readyAssignmentCount' => collect($assignments)
                ->where('is_linked', true)
                ->count(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $module
     * @return array<string, mixed>
     */
    private function prepareModule(array $module): array
    {
        $module['assignments'] = collect($module['assignments'] ?? [])
            ->map(fn (array $assignment): array => $this->prepareAssignment($assignment))
            ->all();

        return $module;
    }

    /**
     * @param  array<string, mixed>  $assignment
     * @return array<string, mixed>
     */
    private function prepareAssignment(array $assignment): array
    {
        $routeName = $assignment['route'] ?? null;
        $url = $assignment['url'] ?? null;

        $href = null;
        if (is_string($routeName) && Route::has($routeName)) {
            $href = route($routeName);
        } elseif (is_string($url) && $url !== '') {
            $href = url($url);
        }

        return $assignment + [
            'href' => $href,
            'source' => $assignment['source'] ?? 'Not connected yet',
            'type' => $assignment['type'] ?? 'Laravel',
            'is_linked' => $href !== null,
        ];
    }
}
