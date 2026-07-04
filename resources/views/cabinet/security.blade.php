@extends('layouts.app', ['title' => 'Security - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-4xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-teal-800">{{ $section['eyebrow'] }}</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $section['title'] }}</h1>
        <p class="text-lg leading-8 text-slate-600">{{ $section['description'] }}</p>
    </section>

    @if (session('status'))
        <section class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-sm font-bold text-emerald-800" role="status">
            {{ session('status') }}
        </section>
    @endif

    @if ($errors->has('github'))
        <section class="rounded-lg border border-orange-300 bg-orange-50 p-4 text-sm font-bold text-orange-800" role="alert">
            {{ $errors->first('github') }}
        </section>
    @endif

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="grid gap-4">
            <article class="rounded-lg border border-stone-300 bg-white p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-950">GitHub identity</h2>
                        <p class="mt-2 leading-7 text-slate-600">
                            Connect GitHub as an external sign-in provider while keeping this Laravel account protected by local session rules.
                        </p>
                    </div>
                    @if ($githubConfigured && $githubRedirectRouteReady)
                        <a class="rounded-lg bg-slate-950 px-4 py-3 text-center text-sm font-bold text-white no-underline transition hover:bg-teal-800" href="{{ route('auth.github.redirect') }}">
                            {{ $githubConnected ? 'Reconnect GitHub' : 'Connect GitHub' }}
                        </a>
                    @else
                        <span class="rounded-lg border border-stone-300 px-4 py-3 text-center text-sm font-bold text-slate-500">OAuth not configured</span>
                    @endif
                </div>

                <p class="mt-4 rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm leading-6 text-slate-600">
                    GitHub uses the account currently signed in at github.com. To connect a different GitHub identity, sign out of GitHub first or switch accounts during authorization.
                </p>

                <dl class="mt-5 grid gap-3 text-sm md:grid-cols-2">
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                        <dt class="font-bold text-slate-500">Connection</dt>
                        <dd class="mt-1 font-bold {{ $githubConnected ? 'text-teal-800' : 'text-orange-700' }}">{{ $githubConnected ? 'Connected' : 'Not connected' }}</dd>
                    </div>
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                        <dt class="font-bold text-slate-500">GitHub username</dt>
                        <dd class="mt-1 break-words font-bold text-slate-950">{{ $user->github_username ?: 'Not connected yet' }}</dd>
                    </div>
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                        <dt class="font-bold text-slate-500">OAuth config</dt>
                        <dd class="mt-1 font-bold {{ $githubConfigured ? 'text-teal-800' : 'text-orange-700' }}">{{ $githubConfigured ? 'Configured' : 'Needs environment variables' }}</dd>
                    </div>
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                        <dt class="font-bold text-slate-500">GitHub MFA</dt>
                        <dd class="mt-1 font-bold text-slate-950">Managed in GitHub</dd>
                    </div>
                </dl>
            </article>

            <article class="rounded-lg border border-stone-300 bg-white p-6">
                <h2 class="text-2xl font-bold text-slate-950">Security checks</h2>
                <div class="mt-5 grid gap-3">
                    @foreach ($checks as $check)
                        @php
                            $toneClasses = [
                                'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                'warning' => 'border-orange-200 bg-orange-50 text-orange-800',
                                'neutral' => 'border-stone-200 bg-stone-50 text-slate-700',
                            ][$check['tone']];
                        @endphp
                        <div class="grid gap-2 rounded-lg border border-stone-200 bg-stone-50 p-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-start">
                            <div>
                                <h3 class="font-bold text-slate-950">{{ $check['label'] }}</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $check['detail'] }}</p>
                            </div>
                            <span class="rounded-lg border px-2.5 py-1 text-xs font-bold uppercase tracking-normal {{ $toneClasses }}">{{ $check['status'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>
        </div>

        <aside class="grid content-start gap-4 rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Security roadmap</h2>
            <div class="grid gap-3 text-sm">
                <div class="border-t border-stone-200 pt-3">
                    <h3 class="font-bold text-slate-950">Now</h3>
                    <p class="mt-1 leading-6 text-slate-600">Session auth, CSRF, role checks, CSP, and GitHub OAuth account linking.</p>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <h3 class="font-bold text-slate-950">Next</h3>
                    <p class="mt-1 leading-6 text-slate-600">Password update flow, recent session review, and security event logging.</p>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <h3 class="font-bold text-slate-950">Later</h3>
                    <p class="mt-1 leading-6 text-slate-600">App MFA with authenticator codes, recovery codes, and confirmation prompts for sensitive changes.</p>
                </div>
            </div>
        </aside>
    </section>
@endsection
