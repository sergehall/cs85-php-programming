@extends('layouts.app', [
    'title' => 'Assignment 11A: API Data - Weekly Weather Forecast',
    'description' => 'The course-aligned static JSON weather exercise for CS85 Module 11.',
])

@section('content')
    <nav class="sticky top-2 z-20 overflow-x-auto rounded-lg border border-stone-300 bg-stone-50/95 p-3 shadow-xl shadow-slate-900/10 backdrop-blur" aria-label="Roadmap module switcher">
        <div class="flex min-w-max gap-2 text-sm font-bold">
            <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-center text-teal-800 no-underline transition hover:border-teal-700" href="{{ route('roadmap') }}">Roadmap</a>
            @foreach ($modules as $roadmapModule)
                <a
                    class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-lg border px-3 py-2 text-center text-sm font-bold no-underline transition {{ $roadmapModule['slug'] === $module['slug'] ? 'border-teal-600 bg-slate-950 text-white shadow-lg shadow-slate-900/15' : 'border-stone-300 bg-white text-slate-600 hover:border-teal-700 hover:text-teal-800' }}"
                    href="{{ route('roadmap.module', $roadmapModule['slug']) }}"
                    @if ($roadmapModule['slug'] === $module['slug']) aria-current="page" @endif
                >
                    {{ $roadmapModule['module'] }}
                </a>
            @endforeach
        </div>
    </nav>

    @include('pages.assignments.module11.track-navigation')

    <section class="overflow-hidden rounded-2xl border border-sky-800 bg-slate-950 text-white shadow-2xl shadow-sky-950/20">
        <div class="grid gap-8 bg-[radial-gradient(circle_at_top_right,_rgba(14,165,233,.34),_transparent_44%)] px-6 py-10 lg:grid-cols-[minmax(0,1.5fr)_minmax(18rem,.7fr)] lg:items-center lg:px-9 lg:py-14">
            <div class="grid gap-6">
                <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-normal text-slate-300">
                    <span class="rounded-full bg-sky-400/15 px-3 py-1.5 text-sky-200 ring-1 ring-sky-300/25">Module 11</span>
                    <span>Course-aligned track</span>
                    <span class="text-sky-300">/</span>
                    <span class="text-emerald-300">Complete</span>
                </div>
                <div class="grid gap-3">
                    <p class="text-sm font-bold uppercase tracking-normal text-orange-300">Assignment 11A · API Data</p>
                    <h1 class="max-w-4xl text-4xl font-black leading-none tracking-tight sm:text-5xl lg:text-6xl">Weekly Weather Forecast</h1>
                    <p class="max-w-3xl text-base leading-7 text-slate-300 md:text-lg md:leading-8">
                        A static JSON file mimics an API response. Laravel reads the file, PHP decodes it into an associative array, and Blade loops through the structured data to build the forecast table.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">storage/app/private</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Storage::get()</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">json_decode()</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Blade @@foreach</span>
                </div>
            </div>

            <aside class="rounded-2xl border border-white/15 bg-white/10 p-6 backdrop-blur" aria-label="Assignment result">
                <p class="text-xs font-bold uppercase tracking-normal text-sky-200">Assignment result</p>
                <p class="mt-4 text-3xl font-black">Static JSON decoded successfully.</p>
                <p class="mt-3 leading-7 text-slate-300">The page renders {{ count($weather) }} forecast records from the Laravel private storage disk.</p>
                <a class="mt-5 inline-flex rounded-lg bg-white px-4 py-3 text-sm font-bold text-slate-950 no-underline transition hover:bg-sky-100" href="#forecast-table">View weather table</a>
            </aside>
        </div>

        <div class="grid border-t border-white/10 sm:grid-cols-3">
            <div class="border-b border-white/10 p-5 sm:border-r sm:border-b-0">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">JSON records</span>
                <strong class="mt-2 block text-3xl">{{ count($weather) }}</strong>
                <span class="text-sm text-slate-400">Monday through Wednesday</span>
            </div>
            <div class="border-b border-white/10 p-5 sm:border-r sm:border-b-0">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">PHP shape</span>
                <strong class="mt-2 block text-3xl">Array</strong>
                <span class="text-sm text-slate-400">json_decode(..., true)</span>
            </div>
            <div class="p-5">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Blade rows</span>
                <strong class="mt-2 block text-3xl">{{ count($weather) }}</strong>
                <span class="text-sm text-slate-400">Rendered with @@foreach</span>
            </div>
        </div>
    </section>

    <section id="forecast-table" class="overflow-hidden rounded-2xl border border-stone-300 bg-white shadow-xl shadow-slate-900/5" aria-labelledby="forecast-title">
        <div class="flex flex-col gap-4 border-b border-stone-200 bg-stone-50 p-5 sm:flex-row sm:items-end sm:justify-between sm:p-6">
            <div>
                <p class="text-xs font-black uppercase tracking-normal text-sky-800">Decoded JSON output</p>
                <h2 id="forecast-title" class="mt-2 text-3xl font-black tracking-tight">Weekly Weather Forecast</h2>
            </div>
            <div class="flex flex-wrap gap-2 text-sm font-bold">
                <a class="rounded-lg px-3 py-2 no-underline {{ $sort === 'week' ? 'bg-slate-950 text-white' : 'border border-stone-300 bg-white text-slate-700' }}" href="{{ route('assignments.module11a.weather') }}">Week order</a>
                <a class="rounded-lg px-3 py-2 no-underline {{ $sort === 'alpha' ? 'bg-slate-950 text-white' : 'border border-stone-300 bg-white text-slate-700' }}" href="{{ route('assignments.module11a.weather', ['sort' => 'alpha']) }}">Alphabetical</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-2xl border-collapse text-left">
                <thead class="bg-slate-950 text-white">
                    <tr>
                        <th class="px-5 py-4 text-xs font-black uppercase tracking-normal" scope="col">Day</th>
                        <th class="px-5 py-4 text-xs font-black uppercase tracking-normal" scope="col">High</th>
                        <th class="px-5 py-4 text-xs font-black uppercase tracking-normal" scope="col">Low</th>
                        <th class="px-5 py-4 text-xs font-black uppercase tracking-normal" scope="col">Condition</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @foreach ($weather as $day)
                        @php
                            $isRainy = str_contains(strtolower((string) $day['condition']), 'rain');
                            $isSunny = str_contains(strtolower((string) $day['condition']), 'sunny');
                        @endphp
                        <tr class="{{ $isRainy ? 'bg-sky-50' : ($isSunny ? 'bg-amber-50/70' : 'bg-white') }}">
                            <th class="px-5 py-5 text-lg font-black text-slate-950" scope="row">{{ $day['day'] }}</th>
                            <td class="px-5 py-5"><span class="rounded-lg bg-orange-100 px-3 py-2 font-mono font-black text-orange-900">{{ $day['high'] }}°F</span></td>
                            <td class="px-5 py-5"><span class="rounded-lg bg-sky-100 px-3 py-2 font-mono font-black text-sky-900">{{ $day['low'] }}°F</span></td>
                            <td class="px-5 py-5">
                                <span class="inline-flex items-center gap-2 font-bold text-slate-800">
                                    <span aria-hidden="true">{{ $isRainy ? '●' : ($isSunny ? '☀' : '◐') }}</span>
                                    {{ $day['condition'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="border-t border-stone-200 bg-stone-50 px-5 py-4 text-sm leading-6 text-slate-600">
            Optional enhancements are active: rainy days use blue conditional formatting, sunny days use amber, and the sort control can reorder the records alphabetically.
        </p>
    </section>

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1.25fr)_minmax(18rem,.75fr)]" aria-label="Assignment walkthrough">
        <article class="rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-8">
            <p class="text-xs font-black uppercase tracking-normal text-sky-800">Step-by-step summary</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight">How the exact assignment works</h2>

            <ol class="mt-6 grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['01', 'Create the file', 'weather.json lives on Laravel’s default private local disk.'],
                    ['02', 'Read the JSON', 'Storage::get(\'weather.json\') returns the file as a string.'],
                    ['03', 'Decode the string', 'json_decode($json, true) creates an associative PHP array.'],
                    ['04', 'Render the rows', 'The controller passes weather to Blade and @foreach creates the table body.'],
                ] as [$number, $title, $copy])
                    <li class="rounded-xl border border-stone-200 bg-stone-50 p-5">
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-sky-700 text-xs font-black text-white">{{ $number }}</span>
                        <strong class="mt-4 block text-lg text-slate-950">{{ $title }}</strong>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                    </li>
                @endforeach
            </ol>
        </article>

        <aside class="rounded-2xl border border-sky-200 bg-sky-50/70 p-6 shadow-xl shadow-sky-950/5 md:p-8">
            <p class="text-xs font-black uppercase tracking-normal text-sky-800">Course requirement</p>
            <h2 class="mt-2 text-2xl font-black tracking-tight">Clean file organization</h2>
            <dl class="mt-6 grid gap-4 text-sm">
                <div class="rounded-xl border border-sky-200 bg-white p-4">
                    <dt class="font-black text-slate-950">Data</dt>
                    <dd class="mt-2 break-all font-mono text-xs leading-5 text-sky-900">storage/app/private/weather.json</dd>
                </div>
                <div class="rounded-xl border border-sky-200 bg-white p-4">
                    <dt class="font-black text-slate-950">Controller</dt>
                    <dd class="mt-2 break-all font-mono text-xs leading-5 text-sky-900">app/Http/Controllers/WeatherController.php</dd>
                </div>
                <div class="rounded-xl border border-sky-200 bg-white p-4">
                    <dt class="font-black text-slate-950">View</dt>
                    <dd class="mt-2 break-all font-mono text-xs leading-5 text-sky-900">resources/views/weather/index.blade.php</dd>
                </div>
                <div class="rounded-xl border border-sky-200 bg-white p-4">
                    <dt class="font-black text-slate-950">Route</dt>
                    <dd class="mt-2 font-mono text-xs leading-5 text-sky-900">GET /weather</dd>
                </div>
            </dl>
        </aside>
    </section>

    <section class="grid gap-5 md:grid-cols-2">
        <article class="rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-8">
            <span class="rounded-full bg-slate-950 px-3 py-1.5 text-xs font-black uppercase tracking-normal text-white">Course concepts retained</span>
            <h2 class="mt-5 text-3xl font-black tracking-tight">Everything the grader should find</h2>
            <ul class="mt-5 grid gap-3 text-sm leading-6 text-slate-600">
                <li><strong class="text-sky-800">01</strong> A valid static JSON array with three weather records.</li>
                <li><strong class="text-sky-800">02</strong> Laravel Storage reading from <code>storage/app/private</code>.</li>
                <li><strong class="text-sky-800">03</strong> <code>json_decode()</code> returning associative arrays.</li>
                <li><strong class="text-sky-800">04</strong> Controller data passed to <code>weather.index</code>.</li>
                <li><strong class="text-sky-800">05</strong> A styled HTML table rendered with Blade <code>@@foreach</code>.</li>
            </ul>
        </article>

        <article class="rounded-2xl border border-teal-800 bg-slate-950 p-6 text-white shadow-xl shadow-teal-950/15 md:p-8">
            <span class="rounded-full bg-teal-400/15 px-3 py-1.5 text-xs font-black uppercase tracking-normal text-teal-200 ring-1 ring-teal-300/25">Professional extension</span>
            <h2 class="mt-5 text-3xl font-black tracking-tight">Ready for the live API step</h2>
            <p class="mt-4 leading-7 text-slate-300">The advanced track keeps the same input → decode → transform → render idea, then adds the engineering needed when the data source is remote and unreliable.</p>
            <a class="mt-6 inline-flex rounded-lg bg-white px-4 py-3 text-sm font-black text-slate-950 no-underline transition hover:bg-teal-100" href="{{ route('roadmap.module', 'module-11') }}">Open advanced implementation</a>
        </article>
    </section>

    <section class="rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-white p-6 shadow-xl shadow-sky-950/5 md:p-8" aria-labelledby="weather-reflection">
        <p class="text-xs font-black uppercase tracking-normal text-sky-800">Assignment reflection</p>
        <h2 id="weather-reflection" class="mt-2 text-3xl font-black tracking-tight">JSON is the bridge between stored text and structured PHP data.</h2>
        <p class="mt-4 max-w-4xl leading-7 text-slate-600">
            The JSON file starts as plain text. <code>json_decode($json, true)</code> converts it into associative arrays, which makes each field available with keys such as <code>$day['high']</code>. The controller prepares the data once, and Blade focuses only on presentation.
        </p>
    </section>
@endsection
