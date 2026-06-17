@extends('layouts.app', ['title' => $title . ' - Cabinet - CS85'])

@section('content')
    @include('partials.cabinet-nav')

    <section class="max-w-3xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal {{ $role === 'Admin' ? 'text-orange-700' : 'text-teal-800' }}">{{ $role }} Area</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">{{ $title }}</h1>
        <p class="text-lg leading-8 text-slate-600">{{ $description }}</p>
    </section>

    <section class="rounded-lg border border-stone-300 bg-white p-6">
        <h2 class="mb-4 text-xl font-bold text-slate-950">Next implementation steps</h2>
        <ul class="grid gap-2 pl-5 leading-7 text-slate-600">
            @foreach ($nextSteps as $step)
                <li>{{ $step }}</li>
            @endforeach
        </ul>
    </section>
@endsection
