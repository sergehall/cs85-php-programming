@extends('layouts.app', ['title' => 'Starter Stack - CS85'])

@section('content')
    @php
        $signals = [
            ['label' => 'Runtime', 'value' => 'PHP 8.4+', 'tone' => 'teal'],
            ['label' => 'Framework', 'value' => 'Laravel 13', 'tone' => 'slate'],
            ['label' => 'Frontend', 'value' => 'Blade + Vite', 'tone' => 'orange'],
            ['label' => 'Database', 'value' => 'Docker MySQL', 'tone' => 'violet'],
        ];

        $ecosystem = [
            [
                'title' => 'Application Core',
                'status' => 'Active',
                'summary' => 'Laravel routes, controllers, Blade layouts, Tailwind components, and Vite assets.',
                'items' => ['MVC foundation', 'Reusable Blade layout', 'Public pages', 'Protected cabinet'],
                'tone' => 'teal',
            ],
            [
                'title' => 'Data Layer',
                'status' => 'Ready',
                'summary' => 'Local SQLite speed now, Docker MySQL path prepared for database coursework.',
                'items' => ['Migrations', 'Seeders', 'Eloquent models', 'Persistent Docker volume'],
                'tone' => 'slate',
            ],
            [
                'title' => 'Quality Gates',
                'status' => 'Running',
                'summary' => 'Formatting, static analysis, tests, secret guard, and production asset build.',
                'items' => ['Laravel Pint', 'PHPStan', 'PHPUnit', 'Prettier + Vite build'],
                'tone' => 'orange',
            ],
            [
                'title' => 'Security and Auth',
                'status' => 'Prepared',
                'summary' => 'Session auth, GitHub OAuth foundation, roles, CSRF, CSP, and security headers.',
                'items' => ['User/admin roles', 'GitHub login', 'Secure headers', 'No secrets in CI'],
                'tone' => 'violet',
            ],
        ];

        $workflow = [
            ['step' => 'Infra', 'command' => 'npm run infra:up', 'detail' => 'Starts Docker services for the project.'],
            ['step' => 'App', 'command' => 'npm run dev', 'detail' => 'Runs Laravel and Vite for local work.'],
            ['step' => 'Quality', 'command' => 'composer quality', 'detail' => 'Formats, analyzes, and tests PHP.'],
            ['step' => 'Build', 'command' => 'npm run quality', 'detail' => 'Checks frontend assets and CI safety.'],
        ];

        $upgradeSlots = [
            ['label' => 'API Layer', 'detail' => 'OpenAPI docs, versioned JSON endpoints, and resource responses.'],
            ['label' => 'Database Depth', 'detail' => 'Relationships, indexes, factories, seed data, and query review notes.'],
            ['label' => 'AI Project', 'detail' => 'OpenAI client, prompt templates, cost controls, and privacy boundaries.'],
            ['label' => 'Deployment', 'detail' => 'Production env notes, managed MySQL, queues, cache, and observability.'],
        ];

        $toneClasses = [
            'teal' => 'bg-teal-50 text-teal-800',
            'slate' => 'bg-slate-100 text-slate-800',
            'orange' => 'bg-orange-50 text-orange-700',
            'violet' => 'bg-violet-50 text-violet-700',
        ];
    @endphp

    <section class="grid gap-4 rounded-lg border border-stone-300 bg-white p-5 shadow-xl shadow-slate-900/10 md:grid-cols-[minmax(0,0.82fr)_minmax(360px,1.18fr)] md:items-center">
        <div class="grid gap-3">
            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Starter Stack</p>
            <h1 class="max-w-2xl text-3xl font-bold leading-tight text-slate-950 md:text-4xl">A Laravel ecosystem that can grow with the course.</h1>
            <p class="max-w-2xl text-sm leading-6 text-slate-600 md:text-base md:leading-7">
                The project starts simple for CS85 fundamentals, but every layer already has a clean
                path toward CRUD workflows, admin tools, Docker-backed data, and the AI final project.
            </p>
        </div>

        <div class="grid gap-2 sm:grid-cols-2">
            @foreach ($signals as $signal)
                <article class="grid gap-1 rounded-lg border border-stone-200 bg-stone-50 p-3 md:p-4">
                    <span class="text-xs font-bold uppercase tracking-normal text-slate-500">{{ $signal['label'] }}</span>
                    <strong class="text-xl font-bold {{ $signal['tone'] === 'teal' ? 'text-teal-800' : ($signal['tone'] === 'orange' ? 'text-orange-700' : ($signal['tone'] === 'violet' ? 'text-violet-700' : 'text-slate-950')) }}">{{ $signal['value'] }}</strong>
                </article>
            @endforeach
        </div>
    </section>

    <section class="grid gap-3 lg:grid-cols-4" aria-label="Stack ecosystem">
        @foreach ($ecosystem as $card)
            <article class="grid content-start gap-3 rounded-lg border border-stone-300 bg-white p-4 shadow-lg shadow-slate-900/5">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-lg font-bold leading-6 text-slate-950">{{ $card['title'] }}</h2>
                    <span class="rounded-lg px-2.5 py-1 text-[0.7rem] font-bold uppercase tracking-normal {{ $toneClasses[$card['tone']] }}">{{ $card['status'] }}</span>
                </div>
                <p class="text-sm leading-6 text-slate-600">{{ $card['summary'] }}</p>
                <ul class="grid gap-2 text-sm font-bold text-slate-600">
                    @foreach ($card['items'] as $item)
                        <li class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $card['tone'] === 'teal' ? 'bg-teal-700' : ($card['tone'] === 'orange' ? 'bg-orange-600' : ($card['tone'] === 'violet' ? 'bg-violet-600' : 'bg-slate-500')) }}"></span>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </article>
        @endforeach
    </section>

    <section class="grid gap-4 lg:grid-cols-[minmax(0,0.95fr)_minmax(300px,0.55fr)]">
        <article class="grid h-full gap-3 rounded-lg border border-stone-300 bg-slate-950 p-4 text-white shadow-xl shadow-slate-950/20">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-normal text-teal-300">Local workflow</p>
                    <h2 class="text-xl font-bold">Start, build, verify, repeat.</h2>
                </div>
                <span class="rounded-lg bg-white/10 px-3 py-1 text-xs font-bold text-slate-300">Docker + Laravel + Vite</span>
            </div>

            <div class="grid gap-2 md:grid-cols-4">
                @foreach ($workflow as $item)
                    <div class="grid gap-2 rounded-lg border border-white/10 bg-white/5 p-2.5">
                        <span class="text-xs font-bold uppercase tracking-normal text-teal-300">{{ $item['step'] }}</span>
                        <code class="truncate rounded-md bg-black/30 px-2 py-1 text-xs font-bold text-white">{{ $item['command'] }}</code>
                        <span class="text-xs leading-5 text-slate-300">{{ $item['detail'] }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="grid gap-2 rounded-lg border border-stone-300 bg-white p-4 shadow-lg shadow-slate-900/5">
            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Installed groups</p>
            @foreach ($stack as $group)
                <div class="grid gap-0.5 border-t border-stone-200 pt-2 first:border-t-0 first:pt-0">
                    <h2 class="text-xs font-bold text-slate-950">{{ $group['category'] }}</h2>
                    <p class="text-xs leading-5 text-slate-600">{{ implode(', ', $group['items']) }}</p>
                </div>
            @endforeach
        </article>
    </section>

    <section class="grid gap-3 rounded-lg border border-stone-300 bg-stone-50 p-4 shadow-lg shadow-slate-900/5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Upgrade slots</p>
                <h2 class="text-2xl font-bold text-slate-950">Reserved space for resources and course growth.</h2>
            </div>
            <span class="rounded-lg bg-white px-3 py-2 text-xs font-bold uppercase tracking-normal text-slate-500">Expandable</span>
        </div>

        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($upgradeSlots as $slot)
                <article class="grid gap-2 rounded-lg border border-stone-200 bg-white p-4">
                    <h3 class="text-base font-bold text-slate-950">{{ $slot['label'] }}</h3>
                    <p class="text-sm leading-6 text-slate-600">{{ $slot['detail'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection
