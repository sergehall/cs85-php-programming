@extends('layouts.app', [
    'title' => 'Dashboard - Assignment 10A',
    'description' => 'The authenticated and personalized dashboard required by Module 10 Assignment 10A.',
])

@section('content')
    @include('pages.assignments.module10.track-navigation', ['activeTrack' => 'assignment'])

    <section class="relative isolate overflow-hidden rounded-2xl border border-emerald-800 bg-emerald-950 px-5 py-9 text-white shadow-2xl shadow-emerald-950/20 md:px-9 md:py-12">
        <div class="absolute -right-20 -top-24 -z-10 h-72 w-72 rounded-full bg-emerald-400/20 blur-3xl" aria-hidden="true"></div>
        <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-end">
            <div class="grid gap-4">
                <p class="text-sm font-bold uppercase tracking-normal text-emerald-300">Protected dashboard</p>
                <h1 class="text-4xl font-bold leading-none tracking-tight md:text-6xl">Welcome back, {{ $user->name }}!</h1>
                <p class="max-w-2xl text-base leading-7 text-emerald-100 md:text-lg">Laravel restored your identity from the authenticated session before rendering this page.</p>
            </div>
            <span class="w-fit rounded-full bg-white/10 px-4 py-2 text-sm font-bold text-emerald-100 ring-1 ring-white/20">{{ $user->email }}</span>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-3" aria-label="Authentication evidence">
        <article class="grid gap-3 rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5">
            <span class="text-xs font-bold uppercase tracking-normal text-violet-700">Session</span>
            <h2 class="text-xl font-bold text-slate-950">Identity remembered</h2>
            <p class="text-sm leading-6 text-slate-600">The request reached this page with an authenticated user already attached.</p>
        </article>
        <article class="grid gap-3 rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5">
            <span class="text-xs font-bold uppercase tracking-normal text-emerald-700">Middleware</span>
            <h2 class="text-xl font-bold text-slate-950">Guest access blocked</h2>
            <p class="text-sm leading-6 text-slate-600">The auth checkpoint redirects signed-out visitors to the login screen.</p>
        </article>
        <article class="grid gap-3 rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5">
            <span class="text-xs font-bold uppercase tracking-normal text-orange-700">Personalization</span>
            <h2 class="text-xl font-bold text-slate-950">User name rendered</h2>
            <p class="text-sm leading-6 text-slate-600">The greeting reads the current account instead of displaying static content.</p>
        </article>
    </section>

    <section class="flex flex-wrap gap-3 rounded-2xl border border-stone-300 bg-stone-50 p-6">
        <a class="rounded-lg bg-orange-700 px-4 py-3 text-sm font-bold text-white no-underline transition hover:bg-orange-800" href="{{ route('secret') }}">Open the secret page</a>
        <a class="rounded-lg border border-stone-300 bg-white px-4 py-3 text-sm font-bold text-slate-800 no-underline transition hover:border-slate-600" href="{{ route('assignments.module10a.overview') }}">Assignment overview</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="rounded-lg border border-rose-300 bg-white px-4 py-3 text-sm font-bold text-rose-800 transition hover:border-rose-700" type="submit">Log out</button>
        </form>
    </section>
@endsection
