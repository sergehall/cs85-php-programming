@extends('layouts.app', ['title' => 'Course Roadmap - CS85'])

@section('content')
    <section class="grid gap-2 py-1">
        <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Course Roadmap</p>
        <h1 class="max-w-4xl text-3xl font-bold leading-tight text-slate-950 md:text-4xl">Eight prepared modules for the CS85 build path.</h1>
        <p class="max-w-3xl text-base leading-7 text-slate-600">
            Each module already has its own route and placeholder workspace. As the course progresses,
            assignments, notes, resources, and project evidence can be added without changing the navigation.
        </p>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="CS85 course modules">
        @foreach ($modules as $module)
            <a class="group grid content-start gap-3 rounded-lg border border-stone-300 bg-white p-4 no-underline shadow-lg shadow-slate-900/5 transition hover:-translate-y-0.5 hover:border-teal-700 hover:shadow-slate-900/10" href="{{ route('roadmap.module', $module['slug']) }}">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-teal-50 text-base font-bold text-teal-800">{{ str_replace('Module ', '', $module['module']) }}</span>
                    <span class="rounded-lg bg-stone-100 px-2.5 py-1 text-[0.7rem] font-bold uppercase tracking-normal text-slate-500 group-hover:bg-teal-800 group-hover:text-white">{{ $module['status'] }}</span>
                </div>
                <div class="grid gap-2">
                    <span class="text-xs font-bold text-orange-700">{{ $module['module'] }} - {{ $module['week'] }}</span>
                    <h2 class="text-base font-bold leading-6 text-slate-950">{{ $module['title'] }}</h2>
                    <p class="text-sm leading-6 text-slate-600">{{ $module['description'] }}</p>
                </div>
                <span class="mt-auto text-sm font-bold text-teal-800">Open module</span>
            </a>
        @endforeach
    </section>
@endsection
