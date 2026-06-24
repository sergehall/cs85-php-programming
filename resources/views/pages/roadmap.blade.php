@extends('layouts.app', ['title' => 'Course Roadmap - CS85'])

@section('content')
    @php
        $moduleCollection = collect($modules);
        $weekGroups = $moduleCollection->groupBy('week_number');
        $accentClasses = [
            'teal' => [
                'border' => 'border-t-teal-700 hover:border-teal-700',
                'badge' => 'bg-teal-50 text-teal-800',
                'text' => 'text-teal-800',
            ],
            'coral' => [
                'border' => 'border-t-orange-600 hover:border-orange-700',
                'badge' => 'bg-orange-50 text-orange-700',
                'text' => 'text-orange-700',
            ],
            'sky' => [
                'border' => 'border-t-sky-700 hover:border-sky-700',
                'badge' => 'bg-sky-50 text-sky-800',
                'text' => 'text-sky-800',
            ],
            'gold' => [
                'border' => 'border-t-amber-500 hover:border-amber-600',
                'badge' => 'bg-amber-50 text-amber-700',
                'text' => 'text-amber-700',
            ],
            'violet' => [
                'border' => 'border-t-violet-700 hover:border-violet-700',
                'badge' => 'bg-violet-50 text-violet-700',
                'text' => 'text-violet-700',
            ],
        ];
    @endphp

    <section class="overflow-hidden rounded-lg border border-stone-300 bg-white shadow-xl shadow-slate-900/10">
        <div class="grid gap-4 bg-slate-950 px-5 py-8 text-white md:px-8 md:py-10">
            <p class="text-xs font-bold uppercase tracking-normal text-orange-300">Course Roadmap</p>
            <h1 class="max-w-4xl text-4xl font-bold leading-none md:text-6xl">CS85 modules by week.</h1>
            <p class="max-w-3xl text-base leading-7 text-slate-300 md:text-lg md:leading-8">
                Browse the same module structure from the coursework project, now organized inside the main Laravel
                application at <span class="font-bold text-white">/roadmap</span>.
            </p>
        </div>

        <div class="grid gap-3 border-t border-stone-300 bg-stone-50 p-4 md:grid-cols-4">
            <div class="rounded-lg border border-stone-300 bg-white p-4">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Weeks</span>
                <strong class="mt-1 block text-3xl text-slate-950">{{ $weekGroups->count() }}</strong>
            </div>
            <div class="rounded-lg border border-stone-300 bg-white p-4">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Modules</span>
                <strong class="mt-1 block text-3xl text-slate-950">{{ $moduleCollection->count() }}</strong>
            </div>
            <div class="rounded-lg border border-stone-300 bg-white p-4">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Open Now</span>
                <strong class="mt-1 block text-3xl text-teal-800">{{ $moduleCollection->where('status', 'Open')->count() }}</strong>
            </div>
            <div class="rounded-lg border border-stone-300 bg-white p-4">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Start Date</span>
                <strong class="mt-1 block text-lg text-slate-950">{{ $moduleCollection->first()['start'] }}</strong>
            </div>
        </div>
    </section>

    <section class="grid gap-4" aria-label="CS85 course modules">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div class="grid gap-1">
                <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Modules</p>
                <h2 class="text-2xl font-bold leading-tight text-slate-950 md:text-3xl">Open a module workspace</h2>
            </div>
            <p class="text-sm font-bold text-slate-500">Click a module to view assignments, notes, and resources.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($modules as $module)
                @php
                    $accent = $accentClasses[$module['accent']] ?? $accentClasses['teal'];
                    $assignmentCount = count($module['assignments']);
                @endphp

                <a
                    class="group grid min-h-64 content-between rounded-lg border border-t-4 border-stone-300 {{ $accent['border'] }} bg-white p-5 no-underline shadow-xl shadow-slate-900/5 transition hover:-translate-y-0.5 hover:shadow-slate-900/10"
                    href="{{ route('roadmap.module', $module['slug']) }}"
                >
                    <span class="grid gap-4">
                        <span class="flex items-start justify-between gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg {{ $accent['badge'] }} text-base font-bold">
                                {{ str_replace('Module ', '', $module['module']) }}
                            </span>
                            <span class="rounded-lg bg-stone-100 px-2.5 py-1 text-[0.7rem] font-bold uppercase tracking-normal text-slate-500 group-hover:bg-slate-950 group-hover:text-white">
                                {{ $module['status'] }}
                            </span>
                        </span>

                        <span class="grid gap-2">
                            <span class="text-xs font-bold uppercase tracking-normal {{ $accent['text'] }}">
                                {{ $module['module'] }} - {{ $module['week'] }} - {{ $module['start'] }}
                            </span>
                            <strong class="text-xl leading-6 text-slate-950">{{ $module['title'] }}</strong>
                            <span class="text-sm leading-6 text-slate-600">{{ $module['description'] }}</span>
                        </span>
                    </span>

                    <span class="mt-5 flex items-center justify-between gap-3 border-t border-stone-200 pt-4 text-sm font-bold">
                        <span class="text-slate-500">
                            {{ $assignmentCount }}
                            {{ $assignmentCount === 1 ? 'assignment' : 'assignments' }}
                        </span>
                        <span class="{{ $accent['text'] }}">View module</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="grid gap-4" aria-label="Course schedule">
        <div class="grid gap-2">
            <h2 class="text-3xl font-bold uppercase tracking-normal text-slate-950">Course Schedule</h2>
            <div class="h-1.5 rounded-full bg-orange-500"></div>
            <p class="text-base font-bold italic text-slate-700">*Schedule is subject to change</p>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-300 bg-white shadow-xl shadow-slate-900/5">
            <div class="hidden grid-cols-[0.8fr_0.9fr_1.8fr_4fr] bg-slate-800 text-white md:grid">
                <span class="border-r border-white/25 px-4 py-4 text-sm font-bold">Week</span>
                <span class="border-r border-white/25 px-4 py-4 text-sm font-bold">Module</span>
                <span class="border-r border-white/25 px-4 py-4 text-sm font-bold">Start</span>
                <span class="px-4 py-4 text-sm font-bold">Topic</span>
            </div>

            <div class="divide-y divide-slate-300">
                @foreach ($modules as $module)
                    <a
                        class="grid gap-2 bg-white p-4 no-underline transition odd:bg-slate-50 hover:bg-teal-50 md:grid-cols-[0.8fr_0.9fr_1.8fr_4fr] md:gap-0 md:p-0"
                        href="{{ route('roadmap.module', $module['slug']) }}"
                    >
                        <span class="font-bold text-slate-500 md:border-r md:border-slate-300 md:px-4 md:py-4 md:text-slate-950">
                            <span class="md:hidden">Week </span>{{ $module['week_number'] }}
                        </span>
                        <span class="font-bold text-slate-950 md:border-r md:border-slate-300 md:px-4 md:py-4">
                            {{ str_replace('Module ', '', $module['module']) }}
                        </span>
                        <span class="text-slate-600 md:border-r md:border-slate-300 md:px-4 md:py-4">{{ $module['start'] }}</span>
                        <span class="font-bold text-slate-950 md:px-4 md:py-4">{{ $module['title'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
