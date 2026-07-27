@extends('layouts.app', [
    'title' => 'Module 12: AI-Powered Final Project - CS85',
    'description' => 'A production-oriented Laravel AI assistant with local model routing, streaming, persistence, tools, telemetry, and privacy boundaries.',
])

@section('content')
    <nav class="sticky top-2 z-20 overflow-x-auto rounded-lg border border-stone-300 bg-stone-50/95 p-3 shadow-xl shadow-slate-900/10 backdrop-blur" aria-label="Roadmap module switcher">
        <div class="flex min-w-max gap-2 text-sm font-bold">
            <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-center text-orange-800 no-underline transition hover:border-orange-700" href="{{ route('roadmap') }}">Roadmap</a>
            @foreach ($modules as $roadmapModule)
                <a
                    class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-lg border px-3 py-2 text-center text-sm font-bold no-underline transition {{ $roadmapModule['slug'] === $module['slug'] ? 'border-orange-600 bg-slate-950 text-white shadow-lg shadow-slate-900/15' : 'border-stone-300 bg-white text-slate-600 hover:border-orange-700 hover:text-orange-800' }}"
                    href="{{ route('roadmap.module', $roadmapModule['slug']) }}"
                    @if ($roadmapModule['slug'] === $module['slug']) aria-current="page" @endif
                >
                    {{ $roadmapModule['module'] }}
                </a>
            @endforeach
        </div>
    </nav>

    @include('pages.assignments.module12.track-navigation')

    <section class="overflow-hidden rounded-lg border border-orange-800 bg-slate-950 text-white shadow-2xl shadow-orange-950/20">
        <div class="grid gap-8 bg-[radial-gradient(circle_at_top_right,_rgba(249,115,22,.34),_transparent_44%)] px-6 py-10 lg:grid-cols-[minmax(0,1.5fr)_minmax(18rem,.7fr)] lg:items-center lg:px-9 lg:py-14">
            <div class="grid gap-6">
                <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-normal text-slate-300">
                    <span class="rounded-full bg-orange-400/15 px-3 py-1.5 text-orange-200 ring-1 ring-orange-300/25">Module 12</span>
                    <span>Final Project</span>
                    <span class="text-orange-300">/</span>
                    <span class="text-emerald-300">Complete</span>
                </div>

                <div class="grid gap-3">
                    <p class="text-sm font-bold uppercase tracking-normal text-orange-300">AI-Powered Web Application</p>
                    <h1 class="max-w-4xl text-4xl font-black leading-none tracking-tight sm:text-5xl lg:text-6xl">A private AI workspace owned by Laravel.</h1>
                    <p class="max-w-3xl text-base leading-7 text-slate-300 md:text-lg md:leading-8">
                        The final project evolves a single content request into authenticated, multi-turn conversations with explicit model routing, local OpenAI-compatible inference, streaming, persistence, tools, telemetry, and safe failure behavior.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Provider contract</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Local LM Studio</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">SSE streaming</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Database history</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Allowlisted tools</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Telemetry + retry</span>
                </div>
            </div>

            <aside class="rounded-2xl border border-white/15 bg-white/10 p-6 backdrop-blur" aria-label="Final project entry point">
                <p class="text-xs font-bold uppercase tracking-normal text-orange-200">Protected workspace</p>
                <p class="mt-4 text-3xl font-black">/cabinet/ai</p>
                <p class="mt-3 leading-7 text-slate-300">Authentication, verified email, CSRF protection, ownership checks, and per-user throttling protect every conversation.</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a class="rounded-lg bg-white px-4 py-3 text-sm font-black text-slate-950 no-underline transition hover:bg-orange-100" href="{{ route('cabinet.ai') }}">Open AI workspace</a>
                    <a class="rounded-lg border border-white/20 px-4 py-3 text-sm font-black text-white no-underline transition hover:bg-white/10" href="{{ route('ai.form') }}">Open Assignment 12A</a>
                </div>
            </aside>
        </div>

        <div class="grid border-t border-white/10 sm:grid-cols-2 lg:grid-cols-4">
            <div class="border-b border-white/10 p-5 sm:border-r lg:border-b-0">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Task modes</span>
                <strong class="mt-2 block text-3xl">{{ count($modes) }}</strong>
                <span class="text-sm text-slate-400">General, coding, architecture</span>
            </div>
            <div class="border-b border-white/10 p-5 lg:border-b-0 lg:border-r">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Transport</span>
                <strong class="mt-2 block text-3xl">SSE</strong>
                <span class="text-sm text-slate-400">Incremental responses</span>
            </div>
            <div class="border-b border-white/10 p-5 sm:border-r sm:border-b-0">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">History window</span>
                <strong class="mt-2 block text-3xl">{{ config('ai.limits.history_messages') }}</strong>
                <span class="text-sm text-slate-400">Bounded messages per request</span>
            </div>
            <div class="p-5">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Rate limit</span>
                <strong class="mt-2 block text-3xl">{{ config('ai.limits.requests_per_minute') }}/m</strong>
                <span class="text-sm text-slate-400">Per authenticated user</span>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-8" aria-labelledby="progression-title">
        <p class="text-xs font-black uppercase tracking-normal text-orange-800">One concept, two deliverables</p>
        <h2 id="progression-title" class="mt-2 text-3xl font-black tracking-tight">Assignment 12A proves the API call. The final project proves the system.</h2>
        <p class="mt-3 max-w-4xl leading-7 text-slate-600">
            The Assignment 12A browser flow has been verified against the live OpenAI API: Laravel sent the prompt to <strong class="text-slate-950">gpt-4o-mini</strong> and returned an editable completion without exposing the API key.
        </p>
        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-3xl border-collapse text-left text-sm">
                <thead class="bg-slate-950 text-white">
                    <tr>
                        <th class="px-4 py-4 font-black" scope="col">Concern</th>
                        <th class="px-4 py-4 font-black" scope="col">Assignment 12A</th>
                        <th class="px-4 py-4 font-black" scope="col">Final Project</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @foreach ([
                        ['Provider', 'Connected OpenAI API · gpt-4o-mini', 'LM Studio through an OpenAI-compatible provider contract'],
                        ['Interaction', 'One title → one editable draft', 'Private multi-turn conversations with retry'],
                        ['Prompting', 'Role + type + tone assembled by one service', 'Versioned, mode-specific system prompt files'],
                        ['Response', 'Validated JSON body rendered in a textarea', 'Streamed deltas rendered as sanitized Markdown'],
                        ['State', 'Form input preserved after validation or failure', 'Database conversations, messages, and request telemetry'],
                        ['Reliability', 'Timeout, friendly error, failure log, throttling', 'Typed failures, stale retry, bounded context, isolated provider outage'],
                        ['Testing', 'Fake service and Laravel HTTP fakes', 'Fake provider, stream parser, authorization, tools, and browser behavior'],
                    ] as [$concern, $assignment, $final])
                        <tr class="align-top">
                            <th class="bg-stone-50 px-4 py-4 font-black text-slate-950" scope="row">{{ $concern }}</th>
                            <td class="px-4 py-4 leading-6 text-slate-600">{{ $assignment }}</td>
                            <td class="px-4 py-4 leading-6 text-slate-600">{{ $final }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-2xl border border-orange-200 bg-orange-50/60 p-6 shadow-xl shadow-orange-950/5 md:p-8" aria-labelledby="runtime-title">
        <p class="text-xs font-black uppercase tracking-normal text-orange-800">Runtime architecture</p>
        <h2 id="runtime-title" class="mt-2 text-3xl font-black tracking-tight">Every boundary has one owner.</h2>
        <p class="mt-3 max-w-4xl leading-7 text-slate-600">Provider details never leak into controllers or Blade, and the browser never calls the model runtime directly.</p>

        <ol class="mt-6 grid gap-3 md:grid-cols-5">
            @foreach ([
                ['01', 'Browser', 'Creates conversations and consumes same-origin SSE.'],
                ['02', 'Controller', 'Authenticates, authorizes, validates, and streams.'],
                ['03', 'Application', 'Builds context, runs tools, persists, and records telemetry.'],
                ['04', 'Provider', 'Translates typed requests into OpenAI-compatible HTTP.'],
                ['05', 'LM Studio', 'Runs the selected local model without owning app state.'],
            ] as [$number, $title, $copy])
                <li class="rounded-xl border border-orange-200 bg-white p-5">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-orange-700 text-xs font-black text-white">{{ $number }}</span>
                    <strong class="mt-4 block text-lg text-slate-950">{{ $title }}</strong>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="grid gap-5 md:grid-cols-3" aria-label="Configured AI modes">
        @foreach ($modes as $key => $mode)
            <article class="rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5">
                <div class="flex items-start justify-between gap-4">
                    <span class="rounded-full bg-orange-100 px-3 py-1.5 text-xs font-black uppercase text-orange-900">{{ $key }}</span>
                    <span class="font-mono text-xs font-bold text-slate-500">T {{ $mode['temperature'] }}</span>
                </div>
                <p class="mt-5 text-xs font-black uppercase tracking-normal text-orange-800">{{ $mode['model_name'] }}</p>
                <h3 class="mt-2 text-2xl font-black tracking-tight">{{ $mode['label'] }}</h3>
                <p class="mt-3 leading-7 text-slate-600">{{ $mode['description'] }}</p>
                <dl class="mt-5 grid gap-3 text-sm">
                    <div class="rounded-xl bg-stone-50 p-4">
                        <dt class="font-black text-slate-950">Provider identifier</dt>
                        <dd class="mt-2 break-all font-mono text-xs text-slate-600">{{ $mode['model'] }}</dd>
                    </div>
                    <div class="rounded-xl bg-stone-50 p-4">
                        <dt class="font-black text-slate-950">Local profile</dt>
                        <dd class="mt-2 text-slate-600">{{ $mode['model_profile'] }}</dd>
                    </div>
                </dl>
            </article>
        @endforeach
    </section>

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1.15fr)_minmax(20rem,.85fr)]">
        <article class="rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-8">
            <p class="text-xs font-black uppercase tracking-normal text-orange-800">Final Project evidence</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight">Production concerns are implemented, not simulated.</h2>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['Authentication + ownership', 'Every route is protected and conversations resolve through the current user.'],
                    ['Prompt versioning', 'General, coding, and architecture instructions live in reviewed resource files.'],
                    ['Safe tool calling', 'Only three read-only course tools are schema-limited and allowlisted.'],
                    ['Output safety', 'Raw model HTML is stripped before Laravel-rendered Markdown reaches the DOM.'],
                    ['Observability', 'Latency, provider, model, status, error codes, and available token usage are stored.'],
                    ['Graceful degradation', 'A local provider outage returns a safe stream error without breaking the rest of the app.'],
                ] as [$title, $copy])
                    <div class="rounded-xl border border-stone-200 bg-stone-50 p-5">
                        <strong class="block text-slate-950">{{ $title }}</strong>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                    </div>
                @endforeach
            </div>
        </article>

        <aside class="rounded-2xl border border-orange-800 bg-slate-950 p-6 text-white shadow-xl shadow-orange-950/15 md:p-8">
            <p class="text-xs font-black uppercase tracking-normal text-orange-200">Documentation map</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight">The implementation contract is versioned with the code.</h2>
            <ul class="mt-6 grid gap-3 text-sm">
                @foreach ([
                    ['AI architecture', 'docs/architecture/ai-architecture.md'],
                    ['Model runtime', 'docs/architecture/ai-model-runtime.md'],
                    ['Routing strategy', 'docs/architecture/ai-routing.md'],
                    ['Database design', 'docs/architecture/ai-database.md'],
                    ['Local setup', 'docs/architecture/ai-local-setup.md'],
                ] as [$label, $path])
                    <li class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <strong class="block text-white">{{ $label }}</strong>
                        <span class="mt-2 block break-all font-mono text-xs leading-5 text-slate-300">{{ $path }}</span>
                    </li>
                @endforeach
            </ul>
            <a class="mt-6 inline-flex rounded-lg bg-white px-4 py-3 text-sm font-black text-slate-950 no-underline transition hover:bg-orange-100" href="https://github.com/SergeHall/cs85-php-programming/tree/main/docs/architecture">View architecture on GitHub</a>
        </aside>
    </section>

    <section class="rounded-2xl border border-orange-200 bg-gradient-to-br from-orange-50 to-white p-6 shadow-xl shadow-orange-950/5 md:p-8">
        <p class="text-xs font-black uppercase tracking-normal text-orange-800">Final reflection</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight">The final project is not a larger prompt. It is a safer system around the model.</h2>
        <p class="mt-4 max-w-5xl leading-7 text-slate-600">
            Assignment 12A teaches the visible mechanics of an AI request: configuration, prompt construction, HTTP, response extraction, and errors. The final project keeps those concepts but moves provider transport behind an interface, stores application-owned history, limits model authority, validates every boundary, measures operational behavior, and remains usable when local inference is unavailable.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a class="rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white no-underline transition hover:bg-orange-800" href="{{ route('cabinet.ai') }}">Launch Final Project</a>
            <a class="rounded-lg border border-orange-300 bg-white px-4 py-3 text-sm font-black text-orange-900 no-underline transition hover:border-orange-700" href="{{ route('ai.form') }}">Compare Assignment 12A</a>
            <a class="rounded-lg border border-stone-300 bg-white px-4 py-3 text-sm font-black text-slate-700 no-underline transition hover:border-orange-700" href="{{ route('roadmap') }}">Back to roadmap</a>
        </div>
    </section>
@endsection
