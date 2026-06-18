@extends('layouts.app', ['title' => $section['title'] . ' - Admin - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-4xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-orange-700">{{ $section['eyebrow'] }}</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $section['title'] }}</h1>
        <p class="text-lg leading-8 text-slate-600">{{ $section['description'] }}</p>
    </section>

    <section class="grid gap-5 md:grid-cols-2">
        <article class="rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Prepared capabilities</h2>
            <ul class="mt-5 grid gap-2 pl-5 leading-7 text-slate-600">
                @foreach ($section['items'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </article>

        <article class="rounded-lg border border-stone-300 bg-white p-6">
            <h2 class="text-xl font-bold text-slate-950">Implementation path</h2>
            <ul class="mt-5 grid gap-2 pl-5 leading-7 text-slate-600">
                @foreach ($section['tasks'] as $task)
                    <li>{{ $task }}</li>
                @endforeach
            </ul>
        </article>
    </section>
@endsection
