@extends('layouts.app', ['title' => $section['title'] . ' - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-4xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-teal-800">{{ $section['eyebrow'] }}</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $section['title'] }}</h1>
        <p class="text-lg leading-8 text-slate-600">{{ $section['description'] }}</p>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4" aria-label="{{ $section['title'] }} summary">
        @foreach ($section['summary'] as $item)
            <article class="rounded-lg border border-stone-300 bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-normal text-slate-500">{{ $item['label'] }}</p>
                <strong class="mt-2 block text-lg text-slate-950">{{ $item['value'] }}</strong>
            </article>
        @endforeach
    </section>

    <section class="grid gap-5 lg:grid-cols-3" aria-label="{{ $section['title'] }} panels">
        @foreach ($section['panels'] as $panel)
            <article class="grid content-start gap-4 rounded-lg border border-stone-300 bg-white p-6">
                <h2 class="text-xl font-bold text-slate-950">{{ $panel['title'] }}</h2>
                <ul class="grid gap-2 pl-5 leading-7 text-slate-600">
                    @foreach ($panel['items'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
        @endforeach
    </section>

    <section class="rounded-lg border border-stone-300 bg-white p-6">
        <h2 class="text-xl font-bold text-slate-950">Next implementation steps</h2>
        <div class="mt-5 grid gap-3 md:grid-cols-3">
            @foreach ($section['tasks'] as $task)
                <article class="rounded-lg border border-stone-200 bg-stone-50 p-4">
                    <p class="font-bold text-slate-950">{{ $task['label'] }}</p>
                    <p class="mt-2 text-sm font-bold uppercase tracking-normal text-orange-700">{{ $task['status'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection
