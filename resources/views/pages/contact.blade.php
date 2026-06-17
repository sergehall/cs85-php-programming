@extends('layouts.app', ['title' => 'Contact - CS85'])

@section('content')
    <section class="max-w-3xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-orange-700">Contact</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">Questions, project planning, and collaboration notes.</h1>
        <p class="text-lg leading-8 text-slate-600">
            Contact details are adapted from the Lavoval contact page so course work, portfolio
            planning, and final project support have one clear place to live.
        </p>
    </section>

    <section class="grid gap-5 md:grid-cols-3">
        <article class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-6">
            <span class="text-xs font-bold uppercase tracking-normal text-slate-500">Direct</span>
            <strong class="text-lg text-slate-950">Email</strong>
            <p class="leading-7 text-slate-600">Best for course questions, Laravel architecture, AI feature planning, and project review.</p>
            <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-teal-700 bg-teal-700 px-4 py-2 font-extrabold text-white no-underline" href="mailto:{{ $contact['email'] }}">Send email</a>
        </article>

        @foreach ($contact['profiles'] as $profile)
            <a class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-6 no-underline transition hover:-translate-y-0.5 hover:border-teal-700" href="{{ $profile['href'] }}" target="_blank" rel="noreferrer">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">{{ $profile['label'] }}</span>
                <strong class="text-lg text-slate-950">{{ $profile['handle'] }}</strong>
                <p class="leading-7 text-slate-600">{{ $profile['description'] }}</p>
            </a>
        @endforeach
    </section>

    <section class="rounded-lg border border-stone-300 bg-white p-6">
        <h2 class="mb-4 text-xl font-bold text-slate-950">Good topics for this project</h2>
        <ul class="grid gap-2 pl-5 leading-7 text-slate-600">
            @foreach ($contact['topics'] as $topic)
                <li>{{ $topic }}</li>
            @endforeach
        </ul>
    </section>
@endsection
