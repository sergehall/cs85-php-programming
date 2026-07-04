@extends('layouts.app', ['title' => 'Admin Overview - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-4xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-orange-700">Admin Overview</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">Admin operations dashboard.</h1>
        <p class="text-lg leading-8 text-slate-600">
            Monitor real users, access requests, security adoption, and recent administrative activity from one protected workspace.
        </p>
    </section>

    @include('partials.cabinet.summary-grid', ['items' => $summary, 'label' => 'Admin summary'])

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_24rem]">
        <div class="grid gap-5">
            <article class="rounded-lg border border-stone-300 bg-white p-6">
                <h2 class="text-2xl font-bold text-slate-950">Operational signals</h2>
                <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($signals as $signal)
                        <div class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-normal text-slate-500">{{ $signal['title'] }}</p>
                            <strong class="mt-2 block text-xl text-slate-950">{{ $signal['value'] }}</strong>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $signal['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-lg border border-stone-300 bg-white p-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-950">Quick actions</h2>
                        <p class="mt-2 leading-7 text-slate-600">Open the admin surfaces that are connected to current application behavior.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3">
                    @foreach ($actions as $action)
                        <a class="grid gap-3 rounded-lg border border-stone-200 bg-stone-50 p-4 no-underline transition hover:border-teal-700 hover:bg-white md:grid-cols-[minmax(0,1fr)_auto] md:items-center" href="{{ route($action['route']) }}">
                            <span>
                                <span class="block text-xs font-bold uppercase tracking-normal text-slate-500">{{ $action['status'] }}</span>
                                <strong class="mt-1 block text-lg text-slate-950">{{ $action['title'] }}</strong>
                                <span class="mt-2 block leading-7 text-slate-600">{{ $action['description'] }}</span>
                            </span>
                            <span class="rounded-lg bg-teal-700 px-4 py-3 text-center text-sm font-bold text-white">Open</span>
                        </a>
                    @endforeach
                </div>
            </article>
        </div>

        <aside class="rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-2xl font-bold text-slate-950">Recent admin activity</h2>
            <div class="mt-5 grid gap-3">
                @forelse ($recentAdminActivity as $activity)
                    @php
                        $actor = $activity->actorUser?->name ?? 'System';
                        $subject = $activity->subjectUser?->email ?? 'No subject';
                    @endphp
                    <article class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-normal text-orange-700">{{ $activity->created_at->diffForHumans() }}</p>
                        <h3 class="mt-2 font-bold text-slate-950">{{ $activity->title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $activity->description }}</p>
                        <p class="mt-3 text-xs font-bold uppercase tracking-normal text-slate-500">Actor: {{ $actor }}</p>
                        <p class="mt-1 break-words text-xs font-bold uppercase tracking-normal text-slate-500">Subject: {{ $subject }}</p>
                    </article>
                @empty
                    <p class="rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm leading-6 text-slate-600">No administrative activity has been recorded yet.</p>
                @endforelse
            </div>
        </aside>
    </section>
@endsection
