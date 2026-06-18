@extends('layouts.app', ['title' => 'Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="grid content-start gap-4 py-3">
            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Student Cabinet</p>
            <h1 class="max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">Manage CS85 from one focused workspace.</h1>
            <p class="max-w-3xl text-lg leading-8 text-slate-600">
                The cabinet is organized like a professional account portal: profile, coursework,
                messages, security, and activity each have a clear place before authentication and
                database-backed CRUD are added.
            </p>
        </div>

        <aside class="grid content-start gap-4 rounded-lg border border-stone-300 bg-white p-6">
            <div class="flex items-center gap-4">
                <div class="grid h-16 w-16 shrink-0 place-items-center rounded-lg bg-teal-800 text-xl font-bold text-white">
                    {{ $account['initials'] }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-lg font-bold text-slate-950">{{ $account['name'] }}</p>
                    <p class="truncate text-sm font-bold text-slate-500">{{ $account['email'] }}</p>
                </div>
            </div>
            <dl class="grid gap-3 text-sm">
                <div class="flex justify-between gap-4 border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Course</dt>
                    <dd class="text-right font-bold text-slate-950">{{ $account['course'] }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Role</dt>
                    <dd class="font-bold text-teal-800">{{ $account['role'] }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-stone-200 pt-3">
                    <dt class="font-bold text-slate-500">Status</dt>
                    <dd class="font-bold text-orange-700">{{ $account['status'] }}</dd>
                </div>
            </dl>
        </aside>
    </section>

    @include('partials.cabinet.summary-grid', ['items' => $metrics, 'label' => 'Cabinet metrics'])

    <section class="grid gap-5 md:grid-cols-2" aria-label="Cabinet focus areas">
        @foreach ($focusItems as $item)
            <a class="grid min-h-48 content-start gap-3 rounded-lg border border-stone-300 bg-white p-6 no-underline transition hover:-translate-y-0.5 hover:border-teal-700" href="{{ route($item['route']) }}">
                <span class="text-xs font-bold uppercase tracking-normal text-teal-800">{{ $item['status'] }}</span>
                <strong class="text-xl text-slate-950">{{ $item['title'] }}</strong>
                <p class="leading-7 text-slate-600">{{ $item['description'] }}</p>
            </a>
        @endforeach
    </section>

    <section class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
        <article class="rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Prepared access roles</h2>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                @foreach ($roles as $role)
                    <div class="grid content-start gap-3 rounded-lg border border-stone-200 bg-stone-50 p-5">
                        <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Prepared role</span>
                        <strong class="text-lg text-slate-950">{{ $role['label'] }}</strong>
                        <p class="leading-7 text-slate-600">{{ $role['description'] }}</p>
                        <ul class="grid gap-2 pl-5 leading-7 text-slate-600">
                            @foreach ($role['abilities'] as $ability)
                                <li>{{ $ability }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Activity preview</h2>
            <div class="mt-5 grid gap-4">
                @foreach ($activityItems as $item)
                    <div class="border-l-4 border-teal-800 pl-4">
                        <span class="text-xs font-bold uppercase tracking-normal text-orange-700">{{ $item['time'] }}</span>
                        <strong class="mt-1 block text-base text-slate-950">{{ $item['title'] }}</strong>
                        <p class="mt-1 text-sm leading-6 text-slate-600">{{ $item['detail'] }}</p>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
@endsection
