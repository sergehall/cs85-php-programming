<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use JsonException;

final class WeatherController extends Controller
{
    /**
     * Display the static JSON forecast required by Assignment 11A.
     *
     * @throws JsonException
     */
    public function index(Request $request): View
    {
        $json = Storage::get('weather.json');
        $weatherData = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        abort_unless(is_array($weatherData), 500, 'The weather dataset must be a JSON array.');

        $sort = $request->query('sort') === 'alpha' ? 'alpha' : 'week';

        if ($sort === 'alpha') {
            usort(
                $weatherData,
                static fn (array $left, array $right): int => strcasecmp(
                    (string) ($left['day'] ?? ''),
                    (string) ($right['day'] ?? ''),
                ),
            );
        }

        $modules = collect(config('course.modules'));
        $module = $modules->firstWhere('slug', 'module-11');

        abort_unless(is_array($module), 404);

        return view('weather.index', [
            'module' => $module,
            'modules' => $modules,
            'sort' => $sort,
            'weather' => $weatherData,
        ]);
    }
}
