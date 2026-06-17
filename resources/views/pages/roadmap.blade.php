@extends('layouts.app', ['title' => 'Course Roadmap - CS85'])

@section('content')
    <section class="max-w-3xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-orange-700">Course Roadmap</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">From PHP basics to a database-backed Laravel application.</h1>
        <p class="text-lg leading-8 text-slate-600">
            The roadmap follows the CS85 syllabus sequence while leaving room for portfolio-level
            organization, tests, and final project polish.
        </p>
    </section>

    <section class="grid gap-4" aria-label="CS85 course modules">
        @foreach ($modules as $module)
            <article class="grid gap-2 rounded-lg border border-stone-300 bg-white p-6">
                <span class="font-extrabold text-teal-800">{{ $module['week'] }}</span>
                <h2 class="text-xl font-bold text-slate-950">{{ $module['title'] }}</h2>
                <p class="leading-7 text-slate-600">{{ $module['description'] }}</p>
            </article>
        @endforeach
    </section>
@endsection
