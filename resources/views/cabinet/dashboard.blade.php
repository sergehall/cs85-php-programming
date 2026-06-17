@extends('layouts.app', ['title' => 'Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-3xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-orange-700">Cabinet Foundation</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">Personal workspace first, admin rules ready for later.</h1>
        <p class="text-lg leading-8 text-slate-600">
            The cabinet starts as a user workspace for profile, coursework, and messages. Admin
            tools are separated inside the same cabinet so authentication, middleware, and policies
            can be attached without changing the navigation model.
        </p>
    </section>

    <section class="grid gap-5 md:grid-cols-2" aria-label="Prepared access roles">
        @foreach ($roles as $role)
            <article class="grid content-start gap-3 rounded-lg border border-stone-300 bg-white p-6">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Prepared role</span>
                <strong class="text-lg text-slate-950">{{ $role['label'] }}</strong>
                <p class="leading-7 text-slate-600">{{ $role['description'] }}</p>
                <ul class="grid gap-2 pl-5 leading-7 text-slate-600">
                    @foreach ($role['abilities'] as $ability)
                        <li>{{ $ability }}</li>
                    @endforeach
                </ul>
            </article>
        @endforeach
    </section>

    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($sections as $section)
            <a class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-6 no-underline transition hover:-translate-y-0.5 hover:border-teal-700" href="{{ route($section['route']) }}">
                <span class="text-xs font-bold uppercase tracking-normal {{ $section['status'] === 'Admin' ? 'text-orange-700' : 'text-slate-500' }}">{{ $section['status'] }}</span>
                <strong class="text-lg text-slate-950">{{ $section['title'] }}</strong>
                <p class="leading-7 text-slate-600">{{ $section['description'] }}</p>
            </a>
        @endforeach
    </section>
@endsection
