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
            <h2 class="text-2xl font-bold text-slate-950">User roles</h2>
            <div class="mt-5 grid gap-3">
                @foreach ($users as $managedUser)
                    @php
                        $requestStatus = $managedUser->adminAccessRequest?->status ?? 'none';
                        $isCurrentUser = auth()->id() === $managedUser->id;
                    @endphp
                    <div class="grid gap-4 rounded-lg border border-stone-200 bg-stone-50 p-4 lg:grid-cols-[minmax(0,1fr)_10rem_auto] lg:items-center">
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-950">{{ $managedUser->name }}</h3>
                            <p class="mt-1 break-words text-sm leading-6 text-slate-600">{{ $managedUser->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-normal text-slate-500">Role</p>
                            <p class="mt-1 font-bold {{ $managedUser->isAdmin() ? 'text-teal-800' : 'text-slate-950' }}">{{ $managedUser->isAdmin() ? 'Admin' : 'User' }}</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-normal text-slate-500">Request: {{ Str::headline($requestStatus) }}</p>
                        </div>
                        <div>
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
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
@endsection
