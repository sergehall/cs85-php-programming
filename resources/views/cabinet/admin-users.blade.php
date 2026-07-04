@extends('layouts.app', ['title' => 'Users - Admin - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-4xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-orange-700">{{ $section['eyebrow'] }}</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $section['title'] }}</h1>
        <p class="text-lg leading-8 text-slate-600">{{ $section['description'] }}</p>
    </section>

    @if (session('status'))
        <section class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-sm font-bold text-emerald-800" role="status">
            {{ session('status') }}
        </section>
    @endif

    @if ($errors->has('role'))
        <section class="rounded-lg border border-orange-300 bg-orange-50 p-4 text-sm font-bold text-orange-800" role="alert">
            {{ $errors->first('role') }}
        </section>
    @endif

    @if ($errors->has('login'))
        <section class="rounded-lg border border-orange-300 bg-orange-50 p-4 text-sm font-bold text-orange-800" role="alert">
            {{ $errors->first('login') }}
        </section>
    @endif

    @include('partials.cabinet.summary-grid', ['items' => $metrics, 'label' => 'User administration summary'])

    <section class="grid gap-5">
        <article class="rounded-lg border border-stone-300 bg-white p-6">
            <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-950">Admin access requests</h2>
                    <p class="mt-2 leading-7 text-slate-600">Review standard users who requested access to protected admin tools.</p>
                </div>
                <span class="rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-bold text-slate-700">{{ $pendingRequests->count() }} pending</span>
            </div>

            <div class="mt-5 grid gap-3">
                @forelse ($pendingRequests as $accessRequest)
                    <div class="grid gap-4 rounded-lg border border-stone-200 bg-stone-50 p-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-950">{{ $accessRequest->user->name }}</h3>
                            <p class="mt-1 break-words text-sm leading-6 text-slate-600">{{ $accessRequest->user->email }}</p>
                            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Requested {{ $accessRequest->requested_at->diffForHumans() }}</p>
                        </div>
                        <form method="POST" action="{{ route('cabinet.admin.access-requests.approve', $accessRequest) }}">
                            @csrf
                            @method('PATCH')
                            <button class="w-full rounded-lg bg-teal-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-teal-800 md:w-auto" type="submit">
                                Grant admin access
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm leading-6 text-slate-600">No pending admin access requests.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-lg border border-stone-300 bg-white p-6">
            <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-950">User directory</h2>
                    <p class="mt-2 leading-7 text-slate-600">Search accounts and filter by access role, security setup, or admin request state.</p>
                </div>
                <span class="rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-bold text-slate-700">
                    {{ $users->total() }} matching
                </span>
            </div>

            <form class="mt-5 grid gap-3 rounded-lg border border-stone-200 bg-stone-50 p-4 lg:grid-cols-[minmax(12rem,1fr)_11rem_13rem_12rem_auto]" method="GET" action="{{ route('cabinet.admin.users') }}">
                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Search
                    <input
                        class="rounded-lg border border-stone-300 bg-white px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700"
                        name="search"
                        type="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Name, email, GitHub"
                    >
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Role
                    <select class="rounded-lg border border-stone-300 bg-white px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="role">
                        <option value="all" @selected($filters['role'] === 'all')>All roles</option>
                        <option value="admin" @selected($filters['role'] === 'admin')>Admin</option>
                        <option value="user" @selected($filters['role'] === 'user')>User</option>
                    </select>
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Security
                    <select class="rounded-lg border border-stone-300 bg-white px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="security">
                        <option value="all" @selected($filters['security'] === 'all')>All security</option>
                        <option value="mfa_enabled" @selected($filters['security'] === 'mfa_enabled')>MFA enabled</option>
                        <option value="mfa_missing" @selected($filters['security'] === 'mfa_missing')>MFA missing</option>
                        <option value="github_connected" @selected($filters['security'] === 'github_connected')>GitHub connected</option>
                        <option value="github_missing" @selected($filters['security'] === 'github_missing')>GitHub missing</option>
                        <option value="login_allowed" @selected($filters['security'] === 'login_allowed')>Login allowed</option>
                        <option value="login_blocked" @selected($filters['security'] === 'login_blocked')>Login blocked</option>
                    </select>
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Request
                    <select class="rounded-lg border border-stone-300 bg-white px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="request">
                        <option value="all" @selected($filters['request'] === 'all')>All requests</option>
                        <option value="pending" @selected($filters['request'] === 'pending')>Pending</option>
                        <option value="approved" @selected($filters['request'] === 'approved')>Approved</option>
                        <option value="revoked" @selected($filters['request'] === 'revoked')>Revoked</option>
                        <option value="none" @selected($filters['request'] === 'none')>No request</option>
                    </select>
                </label>

                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-1 lg:content-end">
                    <button class="rounded-lg bg-teal-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-teal-800" type="submit">
                        Apply
                    </button>
                    <a class="rounded-lg border border-stone-300 bg-white px-4 py-3 text-center text-sm font-bold text-slate-700 no-underline transition hover:bg-stone-100" href="{{ route('cabinet.admin.users') }}">
                        Reset
                    </a>
                </div>
            </form>

            <div class="mt-5 grid gap-3">
                @forelse ($users as $managedUser)
                    @php
                        $requestStatus = $managedUser->adminAccessRequest?->status ?? 'none';
                        $isCurrentUser = auth()->id() === $managedUser->id;
                        $hasMfa = $managedUser->hasMfaEnabled();
                        $hasGitHub = is_string($managedUser->github_id) && $managedUser->github_id !== '';
                        $canLogIn = $managedUser->canLogIn();
                    @endphp
                    <div class="grid gap-4 rounded-lg border border-stone-200 bg-stone-50 p-4 xl:grid-cols-[minmax(0,1fr)_18rem_auto] xl:items-center">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-slate-950">{{ $managedUser->name }}</h3>
                                <span class="rounded-lg border px-2 py-1 text-xs font-bold uppercase tracking-normal {{ $managedUser->isAdmin() ? 'border-teal-200 bg-teal-50 text-teal-800' : 'border-stone-200 bg-white text-slate-600' }}">
                                    {{ $managedUser->isAdmin() ? 'Admin' : 'User' }}
                                </span>
                            </div>
                            <p class="mt-1 break-words text-sm leading-6 text-slate-600">{{ $managedUser->email }}</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-normal text-slate-500">
                                Joined {{ $managedUser->created_at->format('M j, Y') }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-lg border px-2 py-1 text-xs font-bold uppercase tracking-normal {{ $hasMfa ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-orange-200 bg-orange-50 text-orange-800' }}">
                                    {{ $hasMfa ? 'MFA enabled' : 'MFA missing' }}
                                </span>
                                <span class="rounded-lg border px-2 py-1 text-xs font-bold uppercase tracking-normal {{ $hasGitHub ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-stone-200 bg-white text-slate-600' }}">
                                    {{ $hasGitHub ? 'GitHub connected' : 'GitHub missing' }}
                                </span>
                                <span class="rounded-lg border px-2 py-1 text-xs font-bold uppercase tracking-normal {{ $canLogIn ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' }}">
                                    {{ $canLogIn ? 'Login allowed' : 'Login blocked' }}
                                </span>
                            </div>
                            <p class="text-xs font-bold uppercase tracking-normal text-slate-500">Request: {{ Str::headline($requestStatus) }}</p>
                            @if ($hasGitHub)
                                <p class="break-words text-xs font-bold uppercase tracking-normal text-slate-500">GitHub: {{ $managedUser->github_username ?? 'Connected' }}</p>
                            @endif
                        </div>

                        <div class="grid gap-2">
                            @if ($managedUser->isAdmin() && ! $isCurrentUser)
                                <form method="POST" action="{{ route('cabinet.admin.users.revoke-admin', $managedUser) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-lg border border-orange-300 bg-white px-4 py-3 text-sm font-bold text-orange-700 transition hover:bg-orange-50" type="submit">
                                        Revoke admin
                                    </button>
                                </form>
                            @elseif ($managedUser->isAdmin())
                                <span class="block rounded-lg border border-stone-200 bg-white px-4 py-3 text-center text-sm font-bold text-slate-500">Current admin</span>
                            @else
                                <span class="block rounded-lg border border-stone-200 bg-white px-4 py-3 text-center text-sm font-bold text-slate-500">Standard user</span>
                            @endif

                            @if (! $managedUser->isAdmin() && $canLogIn)
                                <form method="POST" action="{{ route('cabinet.admin.users.disable-login', $managedUser) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-lg border border-red-300 bg-white px-4 py-3 text-sm font-bold text-red-700 transition hover:bg-red-50" type="submit">
                                        Disable login
                                    </button>
                                </form>
                            @elseif (! $managedUser->isAdmin())
                                <form method="POST" action="{{ route('cabinet.admin.users.enable-login', $managedUser) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="w-full rounded-lg bg-teal-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-teal-800" type="submit">
                                        Allow login
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm leading-6 text-slate-600">No users match the selected filters.</p>
                @endforelse
            </div>

            @if ($users->hasPages())
                <div class="mt-5">
                    {{ $users->links() }}
                </div>
            @endif
        </article>
    </section>
@endsection
