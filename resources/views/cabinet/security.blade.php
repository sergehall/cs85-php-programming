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

    @if ($errors->has('mfa'))
        <section class="rounded-lg border border-orange-300 bg-orange-50 p-4 text-sm font-bold text-orange-800" role="alert">
            {{ $errors->first('mfa') }}
        </section>
    @endif

    @if (is_array($mfaRecoveryCodes) && count($mfaRecoveryCodes) > 0)
        <section class="rounded-lg border border-emerald-300 bg-emerald-50 p-4" role="status">
            <h2 class="text-lg font-bold text-emerald-900">Store these recovery codes now</h2>
            <p class="mt-1 text-sm leading-6 text-emerald-800">They are shown once and can be used if your authenticator app is unavailable.</p>
            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                @foreach ($mfaRecoveryCodes as $recoveryCode)
                    <code class="rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm font-bold text-emerald-900">{{ $recoveryCode }}</code>
                @endforeach
            </div>
        </section>
    @endif

    <section class="grid gap-5">
        <div class="grid gap-4">
            <article class="rounded-lg border border-stone-300 bg-white p-6">
                @php
                    $mfaSecret = is_string($mfaSetup['secret'] ?? null) ? $mfaSetup['secret'] : null;
                    $mfaProvisioningUri = is_string($mfaSetup['provisioning_uri'] ?? null) ? $mfaSetup['provisioning_uri'] : null;
                    $mfaQrCode = is_string($mfaQrCode ?? null) ? $mfaQrCode : null;
                @endphp
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-950">Application MFA</h2>
                        <p class="mt-2 leading-7 text-slate-600">
                            Protect this Laravel account with authenticator app codes and one-time recovery codes.
                        </p>
                    </div>
                    @if ($user->hasMfaEnabled())
                        <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-bold text-emerald-800">MFA enabled</span>
                    @elseif ($mfaSecret)
                        <span class="rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 text-center text-sm font-bold text-orange-800">Setup pending</span>
                    @else
                        <form method="POST" action="{{ route('cabinet.security.mfa.start') }}">
                            @csrf
                            <button class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-teal-800" type="submit">
                                Enable application MFA
                            </button>
                        </form>
                    @endif
                </div>

                @if ($mfaSecret)
                    <div class="mt-5 grid gap-4 rounded-lg border border-stone-200 bg-stone-50 p-4">
                        <div class="grid gap-4 lg:grid-cols-[16rem_minmax(0,1fr)] lg:items-start">
                            <div class="rounded-lg border border-stone-200 bg-white p-4">
                                @if ($mfaQrCode)
                                    <img class="mx-auto h-56 w-56 max-w-full" src="{{ $mfaQrCode }}" alt="Application MFA QR code">
                                @else
                                    <div class="grid h-56 w-full place-items-center rounded-lg border border-dashed border-stone-300 text-center text-sm font-bold text-slate-500">
                                        QR code unavailable
                                    </div>
                                @endif
                            </div>

                            <div class="grid min-w-0 gap-4">
                                <div>
                                    <p class="text-sm font-bold text-slate-950">Manual setup key</p>
                                    <code class="mt-2 block break-all rounded-lg border border-stone-200 bg-white px-3 py-2 text-sm font-bold text-slate-950">{{ $mfaSecret }}</code>
                                </div>
                                @if ($mfaProvisioningUri)
                                    <details class="rounded-lg border border-stone-200 bg-white p-3">
                                        <summary class="cursor-pointer text-sm font-bold text-slate-700">Show setup URI</summary>
                                        <code class="mt-2 block break-all text-xs font-bold leading-5 text-slate-600">{{ $mfaProvisioningUri }}</code>
                                    </details>
                                @endif
                            </div>
                        </div>
                        <form class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end" method="POST" action="{{ route('cabinet.security.mfa.confirm') }}">
                            @csrf
                            <label class="grid gap-2 text-sm font-bold text-slate-700">
                                Six-digit code
                                <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required>
                            </label>
                            <button class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-teal-800" type="submit">
                                Confirm MFA
                            </button>
                        </form>
                    </div>
                @elseif ($user->hasMfaEnabled())
                    <form class="mt-5 grid gap-3 rounded-lg border border-stone-200 bg-stone-50 p-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-end" method="POST" action="{{ route('cabinet.security.mfa.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <label class="grid gap-2 text-sm font-bold text-slate-700">
                            Authenticator or recovery code
                            <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="code" type="text" autocomplete="one-time-code" required>
                        </label>
                        <button class="rounded-lg border border-orange-300 bg-white px-4 py-3 text-sm font-bold text-orange-700 transition hover:bg-orange-50" type="submit">
                            Disable MFA
                        </button>
                    </form>
                @else
                    <dl class="mt-5 grid gap-3 text-sm md:grid-cols-2">
                        <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                            <dt class="font-bold text-slate-500">Status</dt>
                            <dd class="mt-1 font-bold text-orange-700">Not enabled</dd>
                        </div>
                        <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                            <dt class="font-bold text-slate-500">Method</dt>
                            <dd class="mt-1 font-bold text-slate-950">Authenticator app</dd>
                        </div>
                    </dl>
                @endif
            </article>

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

                <div class="mt-4 grid gap-2 rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm leading-6 text-slate-600">
                    <p>
                        Current Laravel account: <strong class="text-slate-950">{{ $user->email }}</strong>
                    </p>
                    <p>
                        GitHub does not look up this email automatically. The button connects whichever GitHub account is currently signed in at github.com. If this Laravel account does not have its own GitHub account yet, create one or sign in to the correct GitHub account before connecting.
                    </p>
                    <p>
                        A GitHub identity can be connected to only one CS85 user at a time.
                    </p>
                    <p>
                        For privacy, CS85 does not reveal whether a GitHub identity is connected to another profile.
                    </p>
                </div>

                <dl class="mt-5 grid gap-3 text-sm md:grid-cols-3">
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
                </dl>
            </article>

            <article class="rounded-lg border border-stone-300 bg-white p-6">
                @php
                    $adminRequestStatus = $adminAccessRequest?->status;
                    $adminRequestLabel = [
                        'pending' => 'Pending admin review',
                        'approved' => 'Approved',
                        'revoked' => 'Revoked',
                    ][$adminRequestStatus] ?? 'Not requested';
                @endphp
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-950">Admin access</h2>
                        <p class="mt-2 leading-7 text-slate-600">
                            Request admin privileges for coursework management. An existing admin must review and grant the role before admin routes unlock.
                        </p>
                    </div>
                    @if ($user->isAdmin())
                        <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-bold text-emerald-800">Admin active</span>
                    @elseif ($adminAccessRequest?->isPending())
                        <span class="rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 text-center text-sm font-bold text-orange-800">Request pending</span>
                    @else
                        <form method="POST" action="{{ route('cabinet.security.admin-access-request') }}">
                            @csrf
                            <button class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-teal-800" type="submit">
                                Request admin access
                            </button>
                        </form>
                    @endif
                </div>

                <dl class="mt-5 grid gap-3 text-sm md:grid-cols-2">
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                        <dt class="font-bold text-slate-500">Current role</dt>
                        <dd class="mt-1 font-bold {{ $user->isAdmin() ? 'text-teal-800' : 'text-slate-950' }}">{{ $user->isAdmin() ? 'Admin' : 'User' }}</dd>
                    </div>
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                        <dt class="font-bold text-slate-500">Access request</dt>
                        <dd class="mt-1 font-bold {{ $adminAccessRequest?->isPending() ? 'text-orange-700' : 'text-slate-950' }}">{{ $adminRequestLabel }}</dd>
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
    </section>
@endsection
