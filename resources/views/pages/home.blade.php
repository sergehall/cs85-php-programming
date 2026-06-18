@extends('layouts.app', ['title' => 'CS85 PHP Programming'])

@section('content')
    @php
        $codeTabs = [
            'blade' => [
                'label' => 'Blade',
                'code' => <<<'CODE'
// resources/views/courses/show.blade.php
@extends('layouts.app')

@section('content')
    <x-course.shell :course="$course">
        @foreach ($modules as $module)
            <x-course.module :module="$module" />
        @endforeach
    </x-course.shell>
@endsection
CODE,
            ],
            'routing' => [
                'label' => 'Routing',
                'code' => <<<'CODE'
// routes/web.php
Route::middleware('auth')
    ->prefix('cabinet')
    ->name('cabinet.')
    ->group(function () {
        Route::get('/', DashboardController::class)
            ->name('dashboard');
    });
CODE,
            ],
            'mysql' => [
                'label' => 'MySQL',
                'code' => <<<'CODE'
// database/migrations
Schema::create('assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('title');
    $table->string('status')->default('draft');
    $table->timestamps();
});
CODE,
            ],
            'auth' => [
                'label' => 'Auth',
                'code' => <<<'CODE'
// app/Http/Middleware/EnsureAdmin.php
public function handle(Request $request, Closure $next): Response
{
    abort_unless($request->user()?->isAdmin(), 403);

    return $next($request);
}
CODE,
            ],
            'ai' => [
                'label' => 'AI',
                'code' => <<<'CODE'
// final-project/ai-assistant.php
$summary = OpenAI::responses()->create([
    'model' => 'gpt-4.1-mini',
    'input' => $validatedCourseQuestion,
]);

return response()->json($summary);
CODE,
            ],
        ];

        $proofPoints = [
            ['value' => '70.9%', 'label' => 'known server-side language share', 'detail' => 'W3Techs - June 2026', 'tone' => 'violet'],
            ['value' => 'MVC', 'label' => 'Laravel structure', 'detail' => 'Routes, controllers, views', 'tone' => 'teal'],
            ['value' => 'Vite', 'label' => 'fast frontend loop', 'detail' => 'Tailwind classes build cleanly', 'tone' => 'orange'],
            ['value' => 'MySQL', 'label' => 'Docker persistence', 'detail' => 'Database-ready coursework', 'tone' => 'slate'],
        ];

        $pipeline = [
            ['step' => 'Request', 'detail' => 'Browser / API'],
            ['step' => 'Route', 'detail' => 'web.php'],
            ['step' => 'Controller', 'detail' => 'Application logic'],
            ['step' => 'View', 'detail' => 'Blade + Tailwind'],
            ['step' => 'Response', 'detail' => 'HTML / JSON'],
        ];
    @endphp

    <section class="grid gap-4">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,0.95fr)_minmax(440px,1.05fr)] lg:items-center">
            <div class="grid gap-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <img class="h-28 w-28 shrink-0 rounded-lg object-cover shadow-2xl shadow-violet-900/20 md:h-36 md:w-36" src="{{ asset('assets/brand/cs85-logo-512.png') }}" width="144" height="144" alt="CS85 PHP Programming logo">
                    <div class="grid gap-2">
                        <p class="text-xs font-bold uppercase tracking-normal text-orange-700 md:text-sm">Santa Monica College - Summer 2026</p>
                        <h1 class="max-w-3xl text-4xl font-bold leading-none text-slate-950 md:text-5xl">PHP still runs the web.</h1>
                    </div>
                </div>

                <p class="max-w-3xl text-base leading-7 text-slate-600 md:text-lg md:leading-8">
                    CS85 turns PHP from a language you hear about into a professional Laravel stack:
                    routing, Blade, Tailwind, MySQL, authentication, security headers, and an AI-ready
                    final project that can grow beyond the classroom.
                </p>

                <div class="flex flex-wrap gap-3">
                    <a class="inline-flex min-h-10 items-center justify-center rounded-lg border border-teal-700 bg-teal-700 px-4 py-2 text-sm font-extrabold text-white no-underline transition hover:-translate-y-0.5 hover:bg-slate-950" href="{{ route('roadmap') }}">View roadmap</a>
                    <a class="inline-flex min-h-10 items-center justify-center rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-extrabold text-slate-950 no-underline transition hover:-translate-y-0.5 hover:border-violet-700 hover:text-violet-800" href="{{ route('cabinet.dashboard') }}">Open cabinet</a>
                    <a class="inline-flex min-h-10 items-center justify-center rounded-lg border border-stone-300 bg-stone-100 px-4 py-2 text-sm font-extrabold text-slate-950 no-underline transition hover:-translate-y-0.5 hover:border-teal-700 hover:text-teal-800" href="{{ route('stack') }}">Inspect stack</a>
                </div>
            </div>

            <article class="overflow-hidden rounded-lg border border-slate-800 bg-slate-950 shadow-2xl shadow-slate-950/25" aria-label="Interactive Laravel code preview">
                <div class="flex flex-wrap border-b border-white/10 bg-slate-900/80">
                    @foreach ($codeTabs as $key => $tab)
                        <button
                            class="min-h-10 flex-1 border-b-2 border-transparent px-3 py-2 text-sm font-bold text-slate-300 transition hover:bg-white/5 hover:text-white aria-selected:border-teal-400 aria-selected:bg-teal-500/15 aria-selected:text-white"
                            type="button"
                            data-code-tab="{{ $key }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        >
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>

                <div class="grid gap-0">
                    @foreach ($codeTabs as $key => $tab)
                        <pre class="{{ $loop->first ? '' : 'hidden' }} min-h-[18rem] overflow-x-auto p-4 text-xs leading-6 text-slate-100 md:text-sm" data-code-panel="{{ $key }}"><code>{{ $tab['code'] }}</code></pre>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 border-t border-white/10 bg-slate-900 px-4 py-3 text-xs font-bold text-slate-400">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-teal-400"></span>Laravel Blade + Tailwind + Vite</span>
                    <span class="text-violet-300">Compiled by Vite. Served by PHP.</span>
                </div>
            </article>
        </div>

        <section class="grid gap-2 rounded-lg border border-stone-300 bg-white p-3 shadow-xl shadow-slate-900/10 md:grid-cols-4 md:p-4" aria-label="PHP and Laravel proof points">
            @foreach ($proofPoints as $point)
                <div class="grid gap-1.5 border-stone-200 py-2 md:border-l md:first:border-l-0 md:pl-4 md:first:pl-0">
                    <strong class="text-2xl font-bold {{ $point['tone'] === 'violet' ? 'text-violet-700' : ($point['tone'] === 'teal' ? 'text-teal-800' : ($point['tone'] === 'orange' ? 'text-orange-700' : 'text-slate-950')) }}">{{ $point['value'] }}</strong>
                    <span class="text-sm font-bold text-slate-950">{{ $point['label'] }}</span>
                    <span class="text-xs leading-5 text-slate-500">{{ $point['detail'] }}</span>
                </div>
            @endforeach
        </section>

        <section class="grid gap-2 rounded-lg border border-stone-300 bg-stone-50 p-3 shadow-xl shadow-slate-900/10 md:grid-cols-5" aria-label="Laravel request lifecycle">
            @foreach ($pipeline as $item)
                <div class="flex items-center gap-2 rounded-lg bg-white p-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-violet-50 text-sm font-bold text-violet-700">{{ $loop->iteration }}</span>
                    <span class="grid min-w-0 gap-1">
                        <strong class="text-sm text-slate-950">{{ $item['step'] }}</strong>
                        <span class="truncate text-xs font-bold text-slate-500">{{ $item['detail'] }}</span>
                    </span>
                </div>
            @endforeach
        </section>
    </section>

    <section class="grid gap-4 md:grid-cols-3" aria-label="Course entry points">
        <a class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-5 no-underline shadow-xl shadow-slate-900/5 transition hover:-translate-y-0.5 hover:border-teal-700 hover:shadow-slate-900/10" href="{{ route('roadmap') }}">
            <span class="grid h-10 w-10 place-items-center rounded-lg bg-teal-50 text-lg font-bold text-teal-800">01</span>
            <strong class="text-lg text-slate-950">Course Roadmap</strong>
            <p class="text-sm leading-6 text-slate-600">Six weeks from PHP fundamentals to Laravel core, databases, auth, and final project polish.</p>
            <span class="mt-auto text-sm font-bold text-teal-800">Explore roadmap</span>
        </a>
        <a class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-5 no-underline shadow-xl shadow-slate-900/5 transition hover:-translate-y-0.5 hover:border-violet-700 hover:shadow-slate-900/10" href="{{ route('stack') }}">
            <span class="grid h-10 w-10 place-items-center rounded-lg bg-violet-50 text-lg font-bold text-violet-700">02</span>
            <strong class="text-lg text-slate-950">Starter Stack</strong>
            <p class="text-sm leading-6 text-slate-600">PHP, Laravel, Blade, Tailwind, Vite, Docker MySQL, tests, static analysis, and security headers.</p>
            <span class="mt-auto text-sm font-bold text-violet-700">See stack details</span>
        </a>
        <a class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-5 no-underline shadow-xl shadow-slate-900/5 transition hover:-translate-y-0.5 hover:border-orange-700 hover:shadow-slate-900/10" href="{{ route('cabinet.dashboard') }}">
            <span class="grid h-10 w-10 place-items-center rounded-lg bg-orange-50 text-lg font-bold text-orange-700">03</span>
            <strong class="text-lg text-slate-950">User Cabinet</strong>
            <p class="text-sm leading-6 text-slate-600">A protected workspace with GitHub login, roles, admin tooling, and future CRUD workflows.</p>
            <span class="mt-auto text-sm font-bold text-orange-700">Open cabinet</span>
        </a>
    </section>
@endsection
