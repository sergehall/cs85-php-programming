@extends('layouts.app', ['title' => 'Admin Tools - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-3xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-orange-700">Admin Rules</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">Admin tools live inside the cabinet.</h1>
        <p class="text-lg leading-8 text-slate-600">
            These screens are intentionally prepared but not yet protected. When auth is introduced,
            this group should receive admin middleware, role checks, policies, and audit logging.
        </p>
    </section>

    <section class="grid gap-5 md:grid-cols-3">
        @foreach ($sections as $section)
            <a class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-6 no-underline transition hover:-translate-y-0.5 hover:border-teal-700" href="{{ route($section['route']) }}">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">{{ $section['status'] }}</span>
                <strong class="text-lg text-slate-950">{{ $section['title'] }}</strong>
                <p class="leading-7 text-slate-600">{{ $section['description'] }}</p>
            </a>
        @endforeach
    </section>
@endsection
