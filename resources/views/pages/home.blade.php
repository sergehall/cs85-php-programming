@extends('layouts.app', ['title' => 'CS85 PHP Programming'])

@section('content')
    <section class="grid gap-5 lg:grid-cols-[minmax(0,1.5fr)_minmax(280px,0.75fr)]">
        <div class="rounded-lg border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/10 md:p-8">
            <div class="mb-6 flex flex-col gap-5 sm:flex-row sm:items-center">
                <img class="h-32 w-32 shrink-0 rounded-lg object-cover shadow-lg shadow-slate-900/20 md:h-40 md:w-40" src="{{ asset('assets/brand/cs85-logo-192.png') }}" width="160" height="160" alt="CS85 PHP Programming logo">
                <div class="grid gap-3">
                    <p class="text-xs font-bold uppercase tracking-normal text-orange-700 md:text-sm">Santa Monica College - Summer 2026</p>
                    <h1 class="max-w-3xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">CS85 PHP Programming</h1>
                </div>
            </div>
            <p class="max-w-3xl text-lg leading-8 text-slate-600">
                A Laravel foundation for learning server-side PHP, building database-backed web
                applications, and growing the final AI-powered project without throwing away the
                early coursework.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-teal-700 bg-teal-700 px-4 py-2 font-extrabold text-white no-underline" href="{{ route('roadmap') }}">View roadmap</a>
                <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-stone-300 bg-stone-100 px-4 py-2 font-extrabold text-slate-950 no-underline" href="{{ route('cabinet.dashboard') }}">Open cabinet</a>
            </div>
        </div>

        <aside class="rounded-lg border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/10 md:p-8" aria-label="Project readiness">
            <h2 class="mb-4 text-xl font-bold text-slate-950">Project Readiness</h2>
            <dl class="grid gap-4">
                <div class="border-t border-stone-300 pt-4">
                    <dt class="mb-1 font-extrabold text-teal-800">Backend</dt>
                    <dd class="m-0 leading-7 text-slate-600">Laravel routes, Blade layouts, feature tests</dd>
                </div>
                <div class="border-t border-stone-300 pt-4">
                    <dt class="mb-1 font-extrabold text-teal-800">Data</dt>
                    <dd class="m-0 leading-7 text-slate-600">SQLite now, MySQL database prepared</dd>
                </div>
                <div class="border-t border-stone-300 pt-4">
                    <dt class="mb-1 font-extrabold text-teal-800">Cabinet</dt>
                    <dd class="m-0 leading-7 text-slate-600">User workspace now, admin rules prepared for later auth</dd>
                </div>
            </dl>
        </aside>
    </section>

    <section class="grid gap-5 md:grid-cols-3" aria-label="Course entry points">
        <a class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-6 no-underline transition hover:-translate-y-0.5 hover:border-teal-700" href="{{ route('roadmap') }}">
            <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Learning plan</span>
            <strong class="text-lg text-slate-950">Course Roadmap</strong>
            <p class="leading-7 text-slate-600">Track the six-week progression from PHP fundamentals to final project polish.</p>
        </a>
        <a class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-6 no-underline transition hover:-translate-y-0.5 hover:border-teal-700" href="{{ route('stack') }}">
            <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Technical base</span>
            <strong class="text-lg text-slate-950">Starter Stack</strong>
            <p class="leading-7 text-slate-600">See the tools, packages, database choices, and quality checks already in place.</p>
        </a>
        <a class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-6 no-underline transition hover:-translate-y-0.5 hover:border-teal-700" href="{{ route('contact') }}">
            <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Support channel</span>
            <strong class="text-lg text-slate-950">Contact</strong>
            <p class="leading-7 text-slate-600">Keep course questions, portfolio planning, and final-project collaboration easy to find.</p>
        </a>
    </section>
@endsection
