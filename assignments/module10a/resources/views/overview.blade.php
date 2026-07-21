@extends('layouts.app', [
    'title' => 'Assignment 10A: Laravel Authentication - CS85',
    'description' => 'The course-aligned Module 10 authentication exercise with registration, login, a personalized dashboard, a protected secret page, and short-answer evidence.',
])

@section('content')
    @include('pages.assignments.module10.track-navigation', ['activeTrack' => 'assignment'])

    <section class="relative isolate overflow-hidden rounded-2xl border border-sky-900 bg-slate-950 px-5 py-9 text-white shadow-2xl shadow-sky-950/20 md:px-9 md:py-12">
        <div class="absolute -right-24 -top-24 -z-10 h-72 w-72 rounded-full bg-sky-500/25 blur-3xl" aria-hidden="true"></div>
        <div class="grid gap-7 lg:grid-cols-[1.25fr_0.75fr] lg:items-end">
            <div class="grid gap-5">
                <div class="flex flex-wrap gap-2 text-xs font-bold uppercase tracking-normal">
                    <span class="rounded-full bg-sky-400/15 px-3 py-1.5 text-sky-200 ring-1 ring-sky-300/25">Assignment 10A</span>
                    <span class="rounded-full bg-emerald-400/15 px-3 py-1.5 text-emerald-200 ring-1 ring-emerald-300/25">Course-aligned track</span>
                </div>
                <div class="grid gap-4">
                    <p class="text-sm font-bold uppercase tracking-normal text-orange-300">User Authentication</p>
                    <h1 class="max-w-4xl text-4xl font-bold leading-none tracking-tight md:text-6xl">Learn the starter-kit flow by using it.</h1>
                    <p class="max-w-3xl text-base leading-7 text-slate-300 md:text-lg md:leading-8">
                        Register, log in, open a personalized dashboard, test an auth-protected secret page, and connect each result to hashing, sessions, middleware, and Laravel routes.
                    </p>
                </div>
            </div>

            <aside class="grid gap-3 rounded-xl border border-white/15 bg-white/8 p-5" aria-label="Framework note">
                <p class="text-xs font-bold uppercase tracking-normal text-sky-200">Framework note</p>
                <p class="text-lg font-bold">Embedded inside the larger portfolio application.</p>
                <p class="text-sm leading-6 text-slate-300">
                    The PDF teaches Laravel 12 Livewire Starter Kit conventions. This track preserves its required routes and learning outcomes while the host application remains the larger Laravel 13 implementation.
                </p>
            </aside>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-[0.9fr_1.1fr]" aria-labelledby="assignment-demo-title">
        <article class="grid content-start gap-4 rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-7">
            <div class="grid gap-2">
                <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Hands-on extension</p>
                <h2 id="assignment-demo-title" class="text-3xl font-bold leading-tight text-slate-950">Run the required flow</h2>
                <p class="leading-7 text-slate-600">The dashboard and secret links intentionally pass through the auth middleware when you are signed out.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <a class="grid min-h-28 content-start gap-2 rounded-xl border border-sky-200 bg-sky-50 p-4 no-underline transition hover:border-sky-700" href="{{ route('register') }}">
                    <span class="text-xs font-bold uppercase tracking-normal text-sky-700">Step 1</span>
                    <strong class="text-lg text-slate-950">Register</strong>
                    <span class="text-sm leading-6 text-slate-600">Create a test account with a securely hashed password.</span>
                </a>
                <a class="grid min-h-28 content-start gap-2 rounded-xl border border-violet-200 bg-violet-50 p-4 no-underline transition hover:border-violet-700" href="{{ route('login') }}">
                    <span class="text-xs font-bold uppercase tracking-normal text-violet-700">Step 2</span>
                    <strong class="text-lg text-slate-950">Log in</strong>
                    <span class="text-sm leading-6 text-slate-600">Authenticate and create a server-side session.</span>
                </a>
                <a class="grid min-h-28 content-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-4 no-underline transition hover:border-emerald-700" href="{{ route('dashboard') }}">
                    <span class="text-xs font-bold uppercase tracking-normal text-emerald-700">Step 3</span>
                    <strong class="text-lg text-slate-950">Open /dashboard</strong>
                    <span class="text-sm leading-6 text-slate-600">See the personalized greeting after authentication.</span>
                </a>
                <a class="grid min-h-28 content-start gap-2 rounded-xl border border-orange-200 bg-orange-50 p-4 no-underline transition hover:border-orange-700" href="{{ route('secret') }}">
                    <span class="text-xs font-bold uppercase tracking-normal text-orange-700">Step 4</span>
                    <strong class="text-lg text-slate-950">Test /secret</strong>
                    <span class="text-sm leading-6 text-slate-600">Watch middleware redirect guests and allow members.</span>
                </a>
            </div>
        </article>

        <article class="grid content-start gap-5 rounded-2xl border border-violet-200 bg-violet-50 p-6 shadow-xl shadow-violet-950/5 md:p-7">
            <div class="grid gap-2">
                <p class="text-xs font-bold uppercase tracking-normal text-violet-700">Required evidence</p>
                <h2 class="text-3xl font-bold leading-tight text-slate-950">Everything the grader should find</h2>
            </div>
            <ul class="grid gap-3 text-sm leading-6 text-slate-700 sm:grid-cols-2">
                <li class="rounded-xl border border-violet-200 bg-white p-4"><strong class="block text-slate-950">Authentication routes</strong><span class="mt-1 block">Registration, login, logout, reset, and verification live in routes/auth.php.</span></li>
                <li class="rounded-xl border border-violet-200 bg-white p-4"><strong class="block text-slate-950">Protected dashboard</strong><span class="mt-1 block">/dashboard greets the authenticated user by name.</span></li>
                <li class="rounded-xl border border-violet-200 bg-white p-4"><strong class="block text-slate-950">Protected secret page</strong><span class="mt-1 block">/secret uses auth middleware and displays Members only.</span></li>
                <li class="rounded-xl border border-violet-200 bg-white p-4"><strong class="block text-slate-950">Concept answers</strong><span class="mt-1 block">README explains authentication, authorization, hashing, sessions, and middleware.</span></li>
                <li class="rounded-xl border border-violet-200 bg-white p-4"><strong class="block text-slate-950">Environment safety</strong><span class="mt-1 block">.env remains excluded from Git.</span></li>
                <li class="rounded-xl border border-violet-200 bg-white p-4"><strong class="block text-slate-950">Screenshot plan</strong><span class="mt-1 block">Five required captures have stable filenames and a checklist.</span></li>
            </ul>
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-stone-300 bg-white shadow-xl shadow-slate-900/5" aria-labelledby="concept-map-title">
        <div class="grid gap-2 border-b border-stone-200 bg-stone-50 p-6 md:p-7">
            <p class="text-xs font-bold uppercase tracking-normal text-teal-700">Read the code</p>
            <h2 id="concept-map-title" class="text-3xl font-bold leading-tight text-slate-950">Concept-to-code map</h2>
        </div>
        <div class="grid divide-y divide-stone-200 md:grid-cols-2 md:divide-x md:divide-y-0">
            <dl class="grid gap-5 p-6 md:p-7">
                <div><dt class="font-bold text-slate-950">Authentication</dt><dd class="mt-1 leading-7 text-slate-600">Verifies identity through registration and login.</dd></div>
                <div><dt class="font-bold text-slate-950">Password hashing</dt><dd class="mt-1 leading-7 text-slate-600">Stores a one-way hash instead of the original password.</dd></div>
                <div><dt class="font-bold text-slate-950">Session</dt><dd class="mt-1 leading-7 text-slate-600">Remembers the authenticated user between HTTP requests.</dd></div>
                <div><dt class="font-bold text-slate-950">Middleware</dt><dd class="mt-1 leading-7 text-slate-600">Redirects guests before dashboard or secret content is rendered.</dd></div>
            </dl>
            <div class="grid content-start gap-4 bg-slate-950 p-6 text-white md:p-7">
                <p class="text-xs font-bold uppercase tracking-normal text-sky-300">Submission files</p>
                <p class="font-mono text-sm leading-7 text-slate-300">routes/auth.php<br>assignments/module10a/routes/web.php<br>assignments/module10a/resources/views/dashboard.blade.php<br>assignments/module10a/resources/views/secret.blade.php<br>assignments/module10a/README.md</p>
                <a class="w-fit rounded-lg bg-white px-4 py-3 text-sm font-bold text-slate-950 no-underline transition hover:bg-sky-100" href="{{ route('roadmap.module', 'module-10') }}">Compare the advanced track</a>
            </div>
        </div>
    </section>
@endsection
