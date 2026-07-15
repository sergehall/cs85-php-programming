@extends('layouts.app', [
    'title' => 'Module 10 Assignment 10A: User Authentication - CS85',
    'description' => 'A visual overview of the Laravel session authentication, authorization, MFA, OAuth, and account security implemented in the CS85 application.',
])

@section('content')
    <nav class="sticky top-2 z-20 overflow-x-auto rounded-xl border border-stone-300 bg-stone-50/95 p-3 shadow-xl shadow-slate-900/10 backdrop-blur" aria-label="Roadmap module switcher">
        <div class="flex min-w-max gap-2 text-sm font-bold">
            <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-center text-teal-800 no-underline transition hover:border-teal-700" href="{{ route('roadmap') }}">Roadmap</a>
            @foreach ($modules as $roadmapModule)
                <a
                    class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-lg border px-3 py-2 text-center text-sm font-bold no-underline transition {{ $roadmapModule['slug'] === $module['slug'] ? 'border-violet-700 bg-slate-950 text-white shadow-lg shadow-slate-900/15' : 'border-stone-300 bg-white text-slate-600 hover:border-violet-700 hover:text-violet-800' }}"
                    href="{{ route('roadmap.module', $roadmapModule['slug']) }}"
                    @if ($roadmapModule['slug'] === $module['slug']) aria-current="page" @endif
                >
                    {{ $roadmapModule['module'] }}
                </a>
            @endforeach
        </div>
    </nav>

    <section class="relative isolate overflow-hidden rounded-2xl border border-violet-900 bg-slate-950 text-white shadow-2xl shadow-violet-950/20">
        <div class="absolute -top-24 right-0 -z-10 h-80 w-80 rounded-full bg-violet-600/25 blur-3xl" aria-hidden="true"></div>
        <div class="absolute -bottom-32 left-1/3 -z-10 h-72 w-72 rounded-full bg-fuchsia-500/15 blur-3xl" aria-hidden="true"></div>

        <div class="grid gap-8 px-5 py-9 md:px-9 md:py-12 lg:grid-cols-[1.35fr_0.65fr] lg:items-end">
            <div class="grid gap-5">
                <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-normal">
                    <span class="rounded-full bg-violet-400/15 px-3 py-1.5 text-violet-200 ring-1 ring-violet-300/25">Module 10</span>
                    <span class="text-slate-400">Assignment 10A</span>
                    <span class="text-slate-600" aria-hidden="true">/</span>
                    <span class="text-emerald-300">Complete</span>
                </div>

                <div class="grid gap-4">
                    <p class="text-sm font-bold uppercase tracking-normal text-orange-300">User Authentication</p>
                    <h1 class="max-w-4xl text-4xl font-bold leading-none tracking-tight md:text-6xl">
                        A user-aware Laravel application.
                    </h1>
                    <p class="max-w-3xl text-base leading-7 text-slate-300 md:text-lg md:leading-8">
                        This application identifies users, protects private screens, separates user and administrator access,
                        and gives every account a secure way to manage passwords, MFA, connected GitHub identity, and active sessions.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2" aria-label="Authentication technologies">
                    <span class="rounded-lg bg-white/8 px-3 py-2 text-xs font-bold text-slate-200 ring-1 ring-white/15">Laravel sessions</span>
                    <span class="rounded-lg bg-white/8 px-3 py-2 text-xs font-bold text-slate-200 ring-1 ring-white/15">Verified email</span>
                    <span class="rounded-lg bg-white/8 px-3 py-2 text-xs font-bold text-slate-200 ring-1 ring-white/15">GitHub OAuth</span>
                    <span class="rounded-lg bg-white/8 px-3 py-2 text-xs font-bold text-slate-200 ring-1 ring-white/15">TOTP MFA</span>
                    <span class="rounded-lg bg-white/8 px-3 py-2 text-xs font-bold text-slate-200 ring-1 ring-white/15">Role authorization</span>
                </div>
            </div>

            <aside class="grid gap-3 rounded-xl border border-white/15 bg-white/8 p-5 backdrop-blur" aria-label="Assignment result">
                <p class="text-xs font-bold uppercase tracking-normal text-violet-200">Assignment result</p>
                <p class="text-2xl font-bold leading-tight">Authentication is implemented and tested.</p>
                <p class="text-sm leading-6 text-slate-300">
                    The final result is not a demo-only login form. It is the authentication layer used by the cabinet and admin areas of this application.
                </p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <a class="rounded-lg bg-white px-4 py-2.5 text-sm font-bold text-slate-950 no-underline transition hover:bg-violet-100" href="{{ route('register') }}">Create account</a>
                    <a class="rounded-lg border border-white/25 px-4 py-2.5 text-sm font-bold text-white no-underline transition hover:border-white/50 hover:bg-white/10" href="{{ route('login') }}">View login</a>
                </div>
            </aside>
        </div>

        <div class="grid border-t border-white/10 bg-black/15 sm:grid-cols-2 lg:grid-cols-4">
            <div class="border-b border-white/10 p-5 sm:border-r lg:border-b-0">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Sign-in methods</span>
                <strong class="mt-1 block text-3xl text-white">2</strong>
                <span class="text-sm text-slate-300">Password and GitHub</span>
            </div>
            <div class="border-b border-white/10 p-5 lg:border-r lg:border-b-0">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Cabinet gates</span>
                <strong class="mt-1 block text-3xl text-white">3</strong>
                <span class="text-sm text-slate-300">Auth, enabled, verified</span>
            </div>
            <div class="border-b border-white/10 p-5 sm:border-r sm:border-b-0 lg:border-r">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Auth limiters</span>
                <strong class="mt-1 block text-3xl text-white">6</strong>
                <span class="text-sm text-slate-300">Abuse-resistant entry points</span>
            </div>
            <div class="p-5">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Audit sinks</span>
                <strong class="mt-1 block text-3xl text-white">2</strong>
                <span class="text-sm text-slate-300">Timeline and security log</span>
            </div>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-[0.8fr_1.2fr]" aria-labelledby="assignment-answer-title">
        <article class="grid content-start gap-4 rounded-2xl border border-violet-200 bg-violet-50 p-6 shadow-xl shadow-violet-950/5 md:p-7">
            <span class="w-fit rounded-full bg-violet-700 px-3 py-1.5 text-xs font-bold uppercase tracking-normal text-white">My answer</span>
            <h2 id="assignment-answer-title" class="text-3xl font-bold leading-tight text-slate-950">What did I build?</h2>
            <p class="text-base leading-7 text-slate-700">
                I built session-based user authentication with Laravel. A visitor can create an account, verify an email address,
                sign in with a password or GitHub, complete MFA when enabled, enter protected cabinet pages, and sign out safely.
            </p>
            <p class="text-base leading-7 text-slate-700">
                Authorization is separate from authentication: standard users manage their own account, while administrator routes
                require an admin role and recent security confirmation for sensitive changes.
            </p>
        </article>

        <article class="grid gap-5 rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-7">
            <div class="grid gap-2">
                <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Core assignment requirements</p>
                <h2 class="text-3xl font-bold leading-tight text-slate-950">Required behavior, delivered.</h2>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="flex gap-3 rounded-xl border border-stone-200 bg-stone-50 p-4">
                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-100 text-sm font-black text-emerald-800" aria-hidden="true">✓</span>
                    <span><strong class="block text-slate-950">Register and log in</strong><span class="mt-1 block text-sm leading-6 text-slate-600">Validated user creation and secure credential checks.</span></span>
                </div>
                <div class="flex gap-3 rounded-xl border border-stone-200 bg-stone-50 p-4">
                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-100 text-sm font-black text-emerald-800" aria-hidden="true">✓</span>
                    <span><strong class="block text-slate-950">Session state</strong><span class="mt-1 block text-sm leading-6 text-slate-600">Laravel remembers the authenticated user between requests.</span></span>
                </div>
                <div class="flex gap-3 rounded-xl border border-stone-200 bg-stone-50 p-4">
                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-100 text-sm font-black text-emerald-800" aria-hidden="true">✓</span>
                    <span><strong class="block text-slate-950">Protected content</strong><span class="mt-1 block text-sm leading-6 text-slate-600">Guests cannot open the private cabinet.</span></span>
                </div>
                <div class="flex gap-3 rounded-xl border border-stone-200 bg-stone-50 p-4">
                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-emerald-100 text-sm font-black text-emerald-800" aria-hidden="true">✓</span>
                    <span><strong class="block text-slate-950">Roles and logout</strong><span class="mt-1 block text-sm leading-6 text-slate-600">Admin boundaries are enforced and logout invalidates the session.</span></span>
                </div>
            </div>
        </article>
    </section>

    <section class="grid gap-5 rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-8" aria-labelledby="auth-flow-title">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div class="grid gap-2">
                <p class="text-xs font-bold uppercase tracking-normal text-violet-700">Authentication journey</p>
                <h2 id="auth-flow-title" class="text-3xl font-bold leading-tight text-slate-950">How a user reaches the cabinet</h2>
            </div>
            <p class="max-w-xl text-sm leading-6 text-slate-600">Each step narrows trust before private application data becomes available.</p>
        </div>

        <ol class="grid gap-3 lg:grid-cols-5">
            <li class="grid content-start gap-3 rounded-xl border border-violet-200 bg-violet-50 p-4">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-violet-700 text-sm font-black text-white">01</span>
                <strong class="text-lg text-slate-950">Choose identity</strong>
                <p class="text-sm leading-6 text-slate-600">Register or sign in with a password or GitHub.</p>
            </li>
            <li class="grid content-start gap-3 rounded-xl border border-stone-200 bg-stone-50 p-4">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-slate-900 text-sm font-black text-white">02</span>
                <strong class="text-lg text-slate-950">Validate account</strong>
                <p class="text-sm leading-6 text-slate-600">Credentials, account status, and verified identity are checked.</p>
            </li>
            <li class="grid content-start gap-3 rounded-xl border border-stone-200 bg-stone-50 p-4">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-slate-900 text-sm font-black text-white">03</span>
                <strong class="text-lg text-slate-950">Complete MFA</strong>
                <p class="text-sm leading-6 text-slate-600">A TOTP or one-time recovery code is required when MFA is enabled.</p>
            </li>
            <li class="grid content-start gap-3 rounded-xl border border-stone-200 bg-stone-50 p-4">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-slate-900 text-sm font-black text-white">04</span>
                <strong class="text-lg text-slate-950">Create session</strong>
                <p class="text-sm leading-6 text-slate-600">Laravel regenerates the session ID and remembers the user safely.</p>
            </li>
            <li class="grid content-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-emerald-700 text-sm font-black text-white">05</span>
                <strong class="text-lg text-slate-950">Enter cabinet</strong>
                <p class="text-sm leading-6 text-slate-600">Middleware confirms authentication, access status, email, and role.</p>
            </li>
        </ol>
    </section>

    <section class="grid gap-4" aria-labelledby="system-nodes-title">
        <div class="grid gap-2">
            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Application map</p>
            <h2 id="system-nodes-title" class="text-3xl font-bold leading-tight text-slate-950">The four main authentication nodes</h2>
            <p class="max-w-3xl text-base leading-7 text-slate-600">The implementation is organized by responsibility, so login logic, account protection, authorization, and audit evidence do not become one large controller.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <article class="grid gap-4 rounded-2xl border border-sky-200 bg-sky-50 p-6">
                <div class="flex items-start justify-between gap-4">
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-sky-700 text-lg font-black text-white" aria-hidden="true">ID</span>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-sky-800 ring-1 ring-sky-200">Entry point</span>
                </div>
                <div class="grid gap-2">
                    <h3 class="text-2xl font-bold text-slate-950">Identity</h3>
                    <p class="leading-7 text-slate-700">Registration, password login, GitHub OAuth, normalized email addresses, and secure password recovery identify the account.</p>
                </div>
                <p class="text-sm font-bold text-sky-800">Routes: /register · /login · /auth/github/*</p>
            </article>

            <article class="grid gap-4 rounded-2xl border border-violet-200 bg-violet-50 p-6">
                <div class="flex items-start justify-between gap-4">
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-violet-700 text-lg font-black text-white" aria-hidden="true">2F</span>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-violet-800 ring-1 ring-violet-200">Account assurance</span>
                </div>
                <div class="grid gap-2">
                    <h3 class="text-2xl font-bold text-slate-950">Verification and MFA</h3>
                    <p class="leading-7 text-slate-700">Signed email verification, TOTP MFA, single-use recovery codes, and recent-auth step-up add proof beyond the first credential.</p>
                </div>
                <p class="text-sm font-bold text-violet-800">Controls: verified · MFA · security.confirmed</p>
            </article>

            <article class="grid gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                <div class="flex items-start justify-between gap-4">
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-emerald-700 text-lg font-black text-white" aria-hidden="true">S</span>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-emerald-800 ring-1 ring-emerald-200">Access boundary</span>
                </div>
                <div class="grid gap-2">
                    <h3 class="text-2xl font-bold text-slate-950">Sessions and roles</h3>
                    <p class="leading-7 text-slate-700">Database sessions support device visibility and revocation. Middleware separates guests, users, disabled accounts, and administrators.</p>
                </div>
                <p class="text-sm font-bold text-emerald-800">Gates: auth · login.enabled · verified · admin</p>
            </article>

            <article class="grid gap-4 rounded-2xl border border-orange-200 bg-orange-50 p-6">
                <div class="flex items-start justify-between gap-4">
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-orange-700 text-lg font-black text-white" aria-hidden="true">A</span>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-orange-800 ring-1 ring-orange-200">Defense and evidence</span>
                </div>
                <div class="grid gap-2">
                    <h3 class="text-2xl font-bold text-slate-950">Limits and audit</h3>
                    <p class="leading-7 text-slate-700">Rate limiters slow abuse, while activity records and structured security logs explain important authentication outcomes.</p>
                </div>
                <p class="text-sm font-bold text-orange-800">Evidence: activity_logs · security log</p>
            </article>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-2" aria-label="Assignment scope comparison">
        <article class="grid content-start gap-4 rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5">
            <div class="grid gap-2">
                <span class="w-fit rounded-full bg-slate-900 px-3 py-1.5 text-xs font-bold uppercase tracking-normal text-white">Assignment foundation</span>
                <h2 class="text-2xl font-bold text-slate-950">What Module 10 demonstrates</h2>
            </div>
            <ul class="grid gap-3 text-sm leading-6 text-slate-700">
                <li class="flex gap-3"><span class="font-black text-violet-700" aria-hidden="true">01</span><span>Laravel authenticates a user and stores identity in a server-side session.</span></li>
                <li class="flex gap-3"><span class="font-black text-violet-700" aria-hidden="true">02</span><span>Middleware protects cabinet pages and redirects guests to login.</span></li>
                <li class="flex gap-3"><span class="font-black text-violet-700" aria-hidden="true">03</span><span>The user role and admin role have different permissions.</span></li>
                <li class="flex gap-3"><span class="font-black text-violet-700" aria-hidden="true">04</span><span>Logout removes authentication state and invalidates the current session.</span></li>
            </ul>
        </article>

        <article class="grid content-start gap-4 rounded-2xl border border-violet-300 bg-slate-950 p-6 text-white shadow-xl shadow-violet-950/15">
            <div class="grid gap-2">
                <span class="w-fit rounded-full bg-violet-400/20 px-3 py-1.5 text-xs font-bold uppercase tracking-normal text-violet-200 ring-1 ring-violet-300/25">Professional extension</span>
                <h2 class="text-2xl font-bold text-white">What I added beyond the minimum</h2>
            </div>
            <ul class="grid gap-3 text-sm leading-6 text-slate-300">
                <li class="flex gap-3"><span class="font-black text-emerald-300" aria-hidden="true">+</span><span>Email verification and password reset with session revocation.</span></li>
                <li class="flex gap-3"><span class="font-black text-emerald-300" aria-hidden="true">+</span><span>Explicit GitHub OAuth account linking without silent email merging.</span></li>
                <li class="flex gap-3"><span class="font-black text-emerald-300" aria-hidden="true">+</span><span>TOTP MFA, single-use recovery codes, and replay prevention.</span></li>
                <li class="flex gap-3"><span class="font-black text-emerald-300" aria-hidden="true">+</span><span>Recent-auth step-up, device session controls, rate limits, and security auditing.</span></li>
            </ul>
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-stone-300 bg-white shadow-xl shadow-slate-900/5" aria-labelledby="implementation-map-title">
        <div class="grid gap-2 border-b border-stone-200 bg-stone-50 p-6 md:p-7">
            <p class="text-xs font-bold uppercase tracking-normal text-violet-700">Implementation map</p>
            <h2 id="implementation-map-title" class="text-3xl font-bold leading-tight text-slate-950">Where each responsibility lives</h2>
            <p class="max-w-3xl leading-7 text-slate-600">A short code map connects the visible behavior to the Laravel application structure.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-176 border-collapse text-left">
                <thead class="bg-slate-900 text-white">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-normal">User action</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-normal">Laravel node</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-normal">Result</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 text-sm">
                    <tr class="bg-white align-top">
                        <td class="px-5 py-4 font-bold text-slate-950">Register or sign in</td>
                        <td class="px-5 py-4 font-mono text-xs text-violet-800">Auth controllers</td>
                        <td class="px-5 py-4 leading-6 text-slate-600">Validate identity, regenerate the session, and record the outcome.</td>
                    </tr>
                    <tr class="bg-stone-50 align-top">
                        <td class="px-5 py-4 font-bold text-slate-950">Open the cabinet</td>
                        <td class="px-5 py-4 font-mono text-xs text-violet-800">auth → login.enabled → verified</td>
                        <td class="px-5 py-4 leading-6 text-slate-600">Allow only authenticated, active, email-verified accounts.</td>
                    </tr>
                    <tr class="bg-white align-top">
                        <td class="px-5 py-4 font-bold text-slate-950">Change security settings</td>
                        <td class="px-5 py-4 font-mono text-xs text-violet-800">security.confirmed</td>
                        <td class="px-5 py-4 leading-6 text-slate-600">Require recent password, MFA, or eligible GitHub proof.</td>
                    </tr>
                    <tr class="bg-stone-50 align-top">
                        <td class="px-5 py-4 font-bold text-slate-950">Manage another user</td>
                        <td class="px-5 py-4 font-mono text-xs text-violet-800">admin middleware</td>
                        <td class="px-5 py-4 leading-6 text-slate-600">Enforce the administrator role and revoke sessions after access changes.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid gap-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-6 md:grid-cols-[1fr_auto] md:items-center md:p-8" aria-labelledby="reflection-title">
        <div class="grid gap-3">
            <p class="text-xs font-bold uppercase tracking-normal text-emerald-800">Assignment reflection</p>
            <h2 id="reflection-title" class="text-3xl font-bold leading-tight text-slate-950">Authentication is a flow, not a single form.</h2>
            <p class="max-w-3xl text-base leading-7 text-slate-700">
                The main lesson from this assignment is that login is only the first decision. A complete application must also protect sessions,
                verify identity, authorize roles, recover accounts safely, limit abuse, and leave useful audit evidence.
            </p>
        </div>
        <div class="flex flex-wrap gap-2 md:max-w-56 md:justify-end">
            <a class="rounded-lg bg-emerald-800 px-4 py-3 text-sm font-bold text-white no-underline transition hover:bg-emerald-900" href="{{ route('cabinet.security') }}">Open security center</a>
            <a class="rounded-lg border border-emerald-300 bg-white px-4 py-3 text-sm font-bold text-emerald-900 no-underline transition hover:border-emerald-700" href="{{ route('roadmap') }}">Back to roadmap</a>
        </div>
    </section>
@endsection
