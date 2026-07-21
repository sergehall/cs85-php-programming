@extends('layouts.app', [
    'title' => 'Members Only - Assignment 10A',
    'description' => 'The custom auth-protected secret page required by Module 10 Assignment 10A.',
])

@section('content')
    @include('pages.assignments.module10.track-navigation', ['activeTrack' => 'assignment'])

    <section class="relative isolate overflow-hidden rounded-2xl border border-orange-800 bg-slate-950 px-5 py-12 text-white shadow-2xl shadow-orange-950/20 md:px-10 md:py-16">
        <div class="absolute -bottom-24 -right-16 -z-10 h-72 w-72 rounded-full bg-orange-500/20 blur-3xl" aria-hidden="true"></div>
        <div class="mx-auto grid max-w-3xl gap-5 text-center">
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-orange-400/15 text-2xl ring-1 ring-orange-300/30" aria-hidden="true">✓</span>
            <p class="text-sm font-bold uppercase tracking-normal text-orange-300">Auth middleware passed</p>
            <h1 class="text-4xl font-bold leading-none tracking-tight md:text-6xl">Members only!</h1>
            <p class="text-lg leading-8 text-slate-300">You can see this protected page because Laravel authenticated {{ $user->name }}.</p>
            <div class="mt-2 flex flex-wrap justify-center gap-3">
                <a class="rounded-lg bg-white px-4 py-3 text-sm font-bold text-slate-950 no-underline transition hover:bg-orange-100" href="{{ route('dashboard') }}">Back to dashboard</a>
                <a class="rounded-lg border border-white/25 px-4 py-3 text-sm font-bold text-white no-underline transition hover:bg-white/10" href="{{ route('assignments.module10a.overview') }}">Assignment overview</a>
            </div>
        </div>
    </section>
@endsection
