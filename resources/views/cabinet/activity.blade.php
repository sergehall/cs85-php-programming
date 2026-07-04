@extends('layouts.app', ['title' => 'Activity - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-4xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-teal-800">{{ $section['eyebrow'] }}</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $section['title'] }}</h1>
        <p class="text-lg leading-8 text-slate-600">{{ $section['description'] }}</p>
    </section>

    <section class="grid gap-3 md:grid-cols-4" aria-label="Activity summary">
        <div class="rounded-lg border border-stone-300 bg-white p-4">
            <p class="text-xs font-bold uppercase tracking-normal text-slate-500">My events</p>
            <p class="mt-2 text-2xl font-bold text-slate-950">{{ $activityCounts['user'] }}</p>
        </div>
        <div class="rounded-lg border border-stone-300 bg-white p-4">
            <p class="text-xs font-bold uppercase tracking-normal text-slate-500">Profile</p>
            <p class="mt-2 text-2xl font-bold text-slate-950">{{ $activityCounts['profile'] }}</p>
        </div>
        <div class="rounded-lg border border-stone-300 bg-white p-4">
            <p class="text-xs font-bold uppercase tracking-normal text-slate-500">Coursework</p>
            <p class="mt-2 text-2xl font-bold text-slate-950">{{ $activityCounts['coursework'] }}</p>
        </div>
        <div class="rounded-lg border border-stone-300 bg-white p-4">
            <p class="text-xs font-bold uppercase tracking-normal text-slate-500">Security</p>
            <p class="mt-2 text-2xl font-bold text-slate-950">{{ $activityCounts['security'] }}</p>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <article class="rounded-lg border border-stone-300 bg-white p-6">
            <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-950">My activity</h2>
                    <p class="mt-2 leading-7 text-slate-600">Profile, coursework, and security actions tied to your account.</p>
                </div>
                <span class="rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-bold text-slate-700">{{ $userActivities->count() }} shown</span>
            </div>

            <div class="mt-5 grid gap-3">
                @forelse ($userActivities as $activity)
                    @php
                        $categoryClasses = [
                            'auth' => 'border-violet-200 bg-violet-50 text-violet-800',
                            'profile' => 'border-teal-200 bg-teal-50 text-teal-800',
                            'coursework' => 'border-sky-200 bg-sky-50 text-sky-800',
                            'security' => 'border-orange-200 bg-orange-50 text-orange-800',
                            'admin' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                        ][$activity->category] ?? 'border-stone-200 bg-stone-50 text-slate-700';
                    @endphp
                    <div class="grid gap-3 rounded-lg border border-stone-200 bg-stone-50 p-4 md:grid-cols-[auto_minmax(0,1fr)]">
                        <span class="rounded-lg border px-2.5 py-1 text-xs font-bold uppercase tracking-normal {{ $categoryClasses }}">{{ Str::headline($activity->category) }}</span>
                        <div class="min-w-0">
                            <div class="flex flex-col gap-1 md:flex-row md:items-start md:justify-between">
                                <h3 class="font-bold text-slate-950">{{ $activity->title }}</h3>
                                <time class="text-xs font-bold uppercase tracking-normal text-slate-500" datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->diffForHumans() }}</time>
                            </div>
                            @if ($activity->description)
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $activity->description }}</p>
                            @endif
                            @if ($activity->actorUser && ! $activity->actorUser->is($activity->subjectUser))
                                <p class="mt-2 text-xs font-bold uppercase tracking-normal text-slate-500">Actor: {{ $activity->actorUser->name }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm leading-6 text-slate-600">
                        No activity yet. Update your profile, open coursework, connect GitHub, or request admin access to start the timeline.
                    </p>
                @endforelse
            </div>
        </article>

        <aside class="grid content-start gap-4 rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Activity sources</h2>
            <div class="grid gap-3 text-sm">
                <div class="border-t border-stone-200 pt-3">
                    <h3 class="font-bold text-slate-950">Profile</h3>
                    <p class="mt-1 leading-6 text-slate-600">Profile updates create user-visible events.</p>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <h3 class="font-bold text-slate-950">Coursework</h3>
                    <p class="mt-1 leading-6 text-slate-600">The coursework workspace records one daily review event.</p>
                </div>
                <div class="border-t border-stone-200 pt-3">
                    <h3 class="font-bold text-slate-950">Security</h3>
                    <p class="mt-1 leading-6 text-slate-600">GitHub connection and admin access requests are tracked.</p>
                </div>
            </div>
        </aside>
    </section>

    @if (auth()->user()?->isAdmin())
        <section class="rounded-lg border border-stone-300 bg-white p-6">
            <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-normal text-orange-700">Admin activity</p>
                    <h2 class="text-2xl font-bold text-slate-950">Administrative timeline</h2>
                    <p class="mt-2 leading-7 text-slate-600">Role requests, grants, and revocations visible only to administrators.</p>
                </div>
                <span class="rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-sm font-bold text-orange-800">{{ $activityCounts['admin'] }} admin events</span>
            </div>

            <div class="mt-5 grid gap-3">
                @forelse ($adminActivities as $activity)
                    <div class="grid gap-4 rounded-lg border border-stone-200 bg-stone-50 p-4 md:grid-cols-[minmax(0,1fr)_14rem] md:items-start">
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-950">{{ $activity->title }}</h3>
                            @if ($activity->description)
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $activity->description }}</p>
                            @endif
                            <p class="mt-2 break-words text-xs font-bold uppercase tracking-normal text-slate-500">
                                Subject: {{ $activity->subjectUser?->email ?? 'Deleted user' }}
                            </p>
                        </div>
                        <div class="text-sm">
                            <p class="font-bold text-slate-950">{{ Str::headline($activity->event) }}</p>
                            <p class="mt-1 text-slate-600">Actor: {{ $activity->actorUser?->name ?? 'System' }}</p>
                            <time class="mt-2 block text-xs font-bold uppercase tracking-normal text-slate-500" datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->diffForHumans() }}</time>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm leading-6 text-slate-600">No administrative activity has been recorded yet.</p>
                @endforelse
            </div>
        </section>
    @endif
@endsection
