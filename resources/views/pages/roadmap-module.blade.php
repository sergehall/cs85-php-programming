@extends('layouts.app', ['title' => $module['title'] . ' - CS85'])

@section('content')
    @php
        $accentClasses = [
            'teal' => [
                'hero' => 'from-slate-950 to-teal-900',
                'badge' => 'bg-teal-50 text-teal-800',
                'text' => 'text-teal-800',
                'border' => 'border-teal-700',
            ],
            'coral' => [
                'hero' => 'from-slate-950 to-orange-900',
                'badge' => 'bg-orange-50 text-orange-700',
                'text' => 'text-orange-700',
                'border' => 'border-orange-700',
            ],
            'sky' => [
                'hero' => 'from-slate-950 to-sky-900',
                'badge' => 'bg-sky-50 text-sky-800',
                'text' => 'text-sky-800',
                'border' => 'border-sky-700',
            ],
            'gold' => [
                'hero' => 'from-slate-950 to-amber-800',
                'badge' => 'bg-amber-50 text-amber-700',
                'text' => 'text-amber-700',
                'border' => 'border-amber-600',
            ],
            'violet' => [
                'hero' => 'from-slate-950 to-violet-950',
                'badge' => 'bg-violet-50 text-violet-700',
                'text' => 'text-violet-700',
                'border' => 'border-violet-700',
            ],
        ];
        $accent = $accentClasses[$module['accent']] ?? $accentClasses['teal'];
    @endphp

    <nav class="sticky top-2 z-20 overflow-x-auto rounded-lg border border-stone-300 bg-stone-50/95 p-3 shadow-xl shadow-slate-900/10 backdrop-blur" aria-label="Roadmap module switcher">
        <div class="flex min-w-max gap-2 text-sm font-bold">
            <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-center text-teal-800 no-underline transition hover:border-teal-700" href="{{ route('roadmap') }}">Roadmap</a>
            @foreach ($modules as $roadmapModule)
                <a
                    class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-lg border px-3 py-2 text-center text-sm font-bold no-underline transition {{ $roadmapModule['slug'] === $module['slug'] ? $accent['border'] . ' bg-slate-950 text-white shadow-lg shadow-slate-900/15' : 'border-stone-300 bg-white text-slate-600 hover:border-teal-700 hover:text-teal-800' }}"
                    href="{{ route('roadmap.module', $roadmapModule['slug']) }}"
                    @if ($roadmapModule['slug'] === $module['slug']) aria-current="page" @endif
                >
                    {{ $roadmapModule['module'] }}
                </a>
            @endforeach
        </div>
    </nav>

    <section class="overflow-hidden rounded-lg border border-stone-300 bg-white shadow-xl shadow-slate-900/10">
        <div class="grid gap-5 bg-gradient-to-br {{ $accent['hero'] }} px-5 py-8 text-white md:px-8 md:py-10">
            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                <div class="grid gap-4">
                    <p class="text-xs font-bold uppercase tracking-normal text-orange-300">
                        {{ $module['module'] }} - {{ $module['week'] }} - {{ $module['start'] }}
                    </p>
                    <h1 class="max-w-4xl text-4xl font-bold leading-none md:text-6xl">{{ $module['title'] }}</h1>
                    <p class="max-w-3xl text-base leading-7 text-slate-200 md:text-lg md:leading-8">{{ $module['description'] }}</p>
                </div>
                <span class="rounded-lg bg-white/10 px-4 py-2 text-sm font-bold uppercase tracking-normal text-white ring-1 ring-white/20">
                    {{ $module['status'] }}
                </span>
            </div>
        </div>

        <div class="grid gap-3 bg-stone-50 p-4 md:grid-cols-4">
            <div class="rounded-lg border border-stone-300 bg-white p-4">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Week</span>
                <strong class="mt-1 block text-2xl text-slate-950">{{ $module['week_number'] }}</strong>
            </div>
            <div class="rounded-lg border border-stone-300 bg-white p-4">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Module</span>
                <strong class="mt-1 block text-2xl text-slate-950">{{ str_replace('Module ', '', $module['module']) }}</strong>
            </div>
            <div class="rounded-lg border border-stone-300 bg-white p-4">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Start</span>
                <strong class="mt-1 block text-base text-slate-950">{{ $module['start'] }}</strong>
            </div>
            <div class="rounded-lg border border-stone-300 bg-white p-4">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Assignments</span>
                <strong class="mt-1 block text-2xl {{ $accent['text'] }}">{{ count($module['assignments']) }}</strong>
            </div>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-3" aria-label="Module workspace">
        <article class="grid min-h-56 content-start gap-4 rounded-lg border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-slate-950">Assignments</h2>
                <span class="rounded-lg {{ $accent['badge'] }} px-2.5 py-1 text-xs font-bold">{{ count($module['assignments']) }}</span>
            </div>

            @forelse ($module['assignments'] as $assignment)
                <div class="grid gap-2 rounded-lg border border-stone-200 bg-stone-50 p-4">
                    <span class="text-xs font-bold uppercase tracking-normal {{ $accent['text'] }}">{{ $assignment['label'] }}</span>
                    <strong class="text-base text-slate-950">{{ $assignment['title'] }}</strong>
                    <p class="text-sm leading-6 text-slate-600">{{ $assignment['description'] }}</p>
                    <span class="text-xs font-bold uppercase tracking-normal text-slate-500">{{ $assignment['status'] }}</span>
                </div>
            @empty
                <p class="leading-7 text-slate-600">Assignments will be added here as this module opens during the course.</p>
            @endforelse
        </article>

        <article class="grid min-h-56 content-start gap-4 rounded-lg border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5">
            <h2 class="text-xl font-bold text-slate-950">Notes</h2>
            @forelse ($module['notes'] as $note)
                <p class="rounded-lg border border-stone-200 bg-stone-50 p-4 leading-7 text-slate-600">{{ $note }}</p>
            @empty
                <p class="leading-7 text-slate-600">Lecture notes, implementation reminders, and code references will live here.</p>
            @endforelse
        </article>

        <article class="grid min-h-56 content-start gap-4 rounded-lg border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5">
            <h2 class="text-xl font-bold text-slate-950">Resources</h2>
            @forelse ($module['resources'] as $resource)
                <span class="rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-bold text-slate-700">{{ $resource }}</span>
            @empty
                <p class="leading-7 text-slate-600">Links, readings, docs, and supporting files will be attached here later.</p>
            @endforelse
        </article>
    </section>
@endsection
