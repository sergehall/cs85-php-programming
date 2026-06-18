@extends('layouts.app', ['title' => $section['title'] . ' - Admin - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-4xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-orange-700">{{ $section['eyebrow'] }}</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $section['title'] }}</h1>
        <p class="text-lg leading-8 text-slate-600">{{ $section['description'] }}</p>
    </section>

    @include('partials.cabinet.panel-list', [
        'label' => $section['title'] . ' admin panels',
        'panels' => [
            ['title' => 'Prepared capabilities', 'items' => $section['items']],
            ['title' => 'Implementation path', 'items' => $section['tasks']],
        ],
    ])
@endsection
