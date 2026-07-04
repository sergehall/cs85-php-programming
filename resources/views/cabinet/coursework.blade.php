@extends('layouts.app', ['title' => 'Coursework - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-4xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-teal-800">{{ $section['eyebrow'] }}</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $section['title'] }}</h1>
        <p class="text-lg leading-8 text-slate-600">{{ $section['description'] }}</p>
    </section>

    <section class="grid gap-4 md:grid-cols-4" aria-label="Coursework summary">
        <article class="rounded-lg border border-stone-300 bg-white p-5">
            <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Modules</span>
            <strong class="mt-2 block text-3xl text-slate-950">{{ count($modules) }}</strong>
        </article>
        <article class="rounded-lg border border-stone-300 bg-white p-5">
            <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Assignments</span>
            <strong class="mt-2 block text-3xl text-slate-950">{{ $assignmentCount }}</strong>
        </article>
        <article class="rounded-lg border border-stone-300 bg-white p-5">
            <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Linked pages</span>
            <strong class="mt-2 block text-3xl text-teal-800">{{ $readyAssignmentCount }}</strong>
        </article>
        <article class="rounded-lg border border-stone-300 bg-white p-5">
            <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Source root</span>
            <strong class="mt-2 block break-words text-base text-slate-950">assignments/</strong>
        </article>
    </section>

    <section class="grid gap-5" aria-label="Coursework assignments">
        @foreach ($modules as $module)
            <article class="rounded-lg border border-stone-300 bg-white p-6">
                <div class="grid gap-3 border-b border-stone-200 pb-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-normal text-teal-800">{{ $module['week'] }} / {{ $module['module'] }}</p>
                        <h2 class="mt-2 text-2xl font-bold text-slate-950">{{ $module['title'] }}</h2>
                        <p class="mt-2 leading-7 text-slate-600">{{ $module['description'] }}</p>
                    </div>
                    <a class="rounded-lg border border-teal-700 px-3 py-2 text-sm font-bold text-teal-800 no-underline transition hover:bg-teal-50" href="{{ route('roadmap.module', $module['slug']) }}">
                        Roadmap
                    </a>
                </div>

                <div class="mt-5 grid gap-4">
                    @forelse ($module['assignments'] as $assignment)
                        <div class="grid gap-4 rounded-lg border border-stone-200 bg-stone-50 p-4 lg:grid-cols-[minmax(0,1fr)_13rem] lg:items-center">
                            <div class="grid gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-lg bg-teal-100 px-2.5 py-1 text-xs font-bold uppercase tracking-normal text-teal-800">{{ $assignment['label'] }}</span>
                                    <span class="rounded-lg bg-orange-100 px-2.5 py-1 text-xs font-bold uppercase tracking-normal text-orange-700">{{ $assignment['type'] }}</span>
                                    <span class="rounded-lg bg-stone-200 px-2.5 py-1 text-xs font-bold uppercase tracking-normal text-slate-600">{{ $assignment['status'] }}</span>
                                </div>
                                <h3 class="text-xl font-bold text-slate-950">{{ $assignment['title'] }}</h3>
                                <p class="leading-7 text-slate-600">{{ $assignment['description'] }}</p>
                            </div>

                            <div class="grid content-start gap-3 text-sm">
                                @if ($assignment['is_linked'])
                                    <a class="rounded-lg bg-teal-800 px-3 py-2 text-center font-bold text-white no-underline transition hover:bg-teal-900" href="{{ $assignment['href'] }}" aria-label="Open {{ $assignment['label'] }}: {{ $assignment['title'] }}">
                                        Open assignment
                                    </a>
                                @else
                                    <span class="rounded-lg border border-stone-300 px-3 py-2 text-center font-bold text-slate-500">Not linked yet</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="rounded-lg border border-stone-200 bg-stone-50 p-4 leading-7 text-slate-600">
                            Assignments will appear here when this module is added to the course configuration.
                        </p>
                    @endforelse
                </div>
            </article>
        @endforeach
    </section>
@endsection
