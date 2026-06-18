@extends('layouts.app', ['title' => $module['title'] . ' - CS85'])

@section('content')
    <nav class="sticky top-2 z-20 overflow-x-auto rounded-lg border border-stone-300 bg-stone-50/95 p-3 shadow-xl shadow-slate-900/10 backdrop-blur" aria-label="Roadmap module switcher">
        <div class="grid min-w-[56rem] grid-cols-9 gap-2 text-sm font-bold">
            <a class="inline-flex min-h-12 items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-3 text-center text-teal-800 no-underline transition hover:border-teal-700" href="{{ route('roadmap') }}">Roadmap</a>
            @foreach ($modules as $roadmapModule)
                <a
                    class="inline-flex min-h-12 items-center justify-center whitespace-nowrap rounded-lg border px-3 py-3 text-center text-sm font-bold no-underline transition {{ $roadmapModule['slug'] === $module['slug'] ? 'border-teal-700 bg-teal-800 text-white shadow-lg shadow-teal-900/15' : 'border-stone-300 bg-white text-slate-600 hover:border-teal-700 hover:text-teal-800' }}"
                    href="{{ route('roadmap.module', $roadmapModule['slug']) }}"
                    @if ($roadmapModule['slug'] === $module['slug']) aria-current="page" @endif
                >
                    {{ $roadmapModule['module'] }}
                </a>
            @endforeach
        </div>
    </nav>

    <section class="grid gap-5 rounded-lg border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/10 md:p-8">
        <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
            <div class="grid gap-4">
                <p class="text-xs font-bold uppercase tracking-normal text-orange-700">{{ $module['module'] }} - {{ $module['week'] }}</p>
                <h1 class="max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $module['title'] }}</h1>
                <p class="max-w-3xl text-lg leading-8 text-slate-600">{{ $module['description'] }}</p>
            </div>
            <span class="rounded-lg bg-teal-50 px-4 py-2 text-sm font-bold uppercase tracking-normal text-teal-800">{{ $module['status'] }}</span>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-3" aria-label="Module workspace placeholders">
        <article class="grid min-h-56 content-start gap-4 rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Assignments</h2>
            @forelse ($module['assignments'] as $assignment)
                <p class="leading-7 text-slate-600">{{ $assignment }}</p>
            @empty
                <p class="leading-7 text-slate-600">Assignments will be added here as this module opens during the course.</p>
            @endforelse
        </article>

        <article class="grid min-h-56 content-start gap-4 rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Notes</h2>
            @forelse ($module['notes'] as $note)
                <p class="leading-7 text-slate-600">{{ $note }}</p>
            @empty
                <p class="leading-7 text-slate-600">Lecture notes, implementation reminders, and code references will live here.</p>
            @endforelse
        </article>

        <article class="grid min-h-56 content-start gap-4 rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Resources</h2>
            @forelse ($module['resources'] as $resource)
                <p class="leading-7 text-slate-600">{{ $resource }}</p>
            @empty
                <p class="leading-7 text-slate-600">Links, readings, docs, and supporting files will be attached here later.</p>
            @endforelse
        </article>
    </section>
@endsection
