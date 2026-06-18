@extends('layouts.app', ['title' => 'Contact - CS85'])

@section('content')
    @php
        [$emailLocal, $emailDomain] = explode('@', $contact['email'], 2);

        $channels = [
            ...collect($contact['profiles'])->map(fn (array $profile): array => [
                'label' => $profile['label'],
                'handle' => $profile['handle'],
                'href' => $profile['href'],
                'description' => $profile['description'],
                'action' => $profile['label'] === 'GitHub' ? 'Open GitHub' : 'Open website',
                'tone' => $profile['label'] === 'GitHub' ? 'slate' : 'orange',
            ])->all(),
        ];

        $badges = ['Course planning', 'Laravel architecture', 'Database work', 'AI final project'];
    @endphp

    <section class="grid gap-3 rounded-lg border border-stone-300 bg-white p-5 shadow-xl shadow-slate-900/10">
        <div class="grid gap-3">
            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Contact</p>
            <h1 class="max-w-3xl text-3xl font-bold leading-tight text-slate-950 md:text-5xl">One place for course questions and project progress.</h1>
            <p class="max-w-3xl text-base leading-7 text-slate-600">
                Use this page as the CS85 communication hub: email for direct questions, GitHub for
                code history, and the coursework site for learning reports and portfolio context.
            </p>
        </div>
    </section>

    <section class="grid gap-3 md:grid-cols-3" aria-label="Contact channels">
        <article class="group grid content-start gap-4 rounded-lg border border-stone-300 bg-white p-5 shadow-lg shadow-slate-900/5 transition hover:-translate-y-0.5 hover:border-teal-700" data-protected-email data-email-local="{{ $emailLocal }}" data-email-domain="{{ $emailDomain }}">
            <div class="flex items-start justify-between gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-lg bg-teal-50 text-base font-bold text-teal-800">E</span>
                <span class="rounded-lg bg-stone-100 px-2.5 py-1 text-[0.7rem] font-bold uppercase tracking-normal text-slate-500 group-hover:bg-slate-950 group-hover:text-white">Protected</span>
            </div>
            <div class="grid gap-2">
                <span class="text-xs font-bold uppercase tracking-normal text-orange-700">Email</span>
                <strong class="break-words text-lg font-bold leading-6 text-slate-950" data-email-display>Protected email</strong>
                <p class="text-sm leading-6 text-slate-600">Best channel for course questions, Laravel planning, project reviews, and AI final project ideas.</p>
            </div>
            <div class="mt-auto flex flex-wrap gap-2">
                <button class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 transition hover:border-teal-700 hover:text-teal-800" type="button" data-email-action="show">Show email</button>
                <button class="rounded-lg border border-teal-700 bg-teal-700 px-3 py-2 text-sm font-bold text-white transition hover:bg-slate-950" type="button" data-email-action="send">Send email</button>
            </div>
        </article>

        @foreach ($channels as $channel)
            <a
                class="group grid content-start gap-4 rounded-lg border border-stone-300 bg-white p-5 no-underline shadow-lg shadow-slate-900/5 transition hover:-translate-y-0.5 {{ $channel['tone'] === 'teal' ? 'hover:border-teal-700' : ($channel['tone'] === 'orange' ? 'hover:border-orange-700' : 'hover:border-slate-700') }}"
                href="{{ $channel['href'] }}"
                @if (! str_starts_with($channel['href'], 'mailto:')) target="_blank" rel="noreferrer" @endif
            >
                <div class="flex items-start justify-between gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-lg text-base font-bold {{ $channel['tone'] === 'teal' ? 'bg-teal-50 text-teal-800' : ($channel['tone'] === 'orange' ? 'bg-orange-50 text-orange-700' : 'bg-slate-100 text-slate-800') }}">{{ substr($channel['label'], 0, 1) }}</span>
                    <span class="rounded-lg bg-stone-100 px-2.5 py-1 text-[0.7rem] font-bold uppercase tracking-normal text-slate-500 group-hover:bg-slate-950 group-hover:text-white">Open</span>
                </div>
                <div class="grid gap-2">
                    <span class="text-xs font-bold uppercase tracking-normal text-orange-700">{{ $channel['label'] }}</span>
                    <strong class="break-words text-lg font-bold leading-6 text-slate-950">{{ $channel['handle'] }}</strong>
                    <p class="text-sm leading-6 text-slate-600">{{ $channel['description'] }}</p>
                </div>
                <span class="mt-auto text-sm font-bold {{ $channel['tone'] === 'teal' ? 'text-teal-800' : ($channel['tone'] === 'orange' ? 'text-orange-700' : 'text-slate-700') }}">{{ $channel['action'] }}</span>
            </a>
        @endforeach
    </section>

    <section class="grid gap-3 rounded-lg border border-stone-300 bg-slate-950 p-5 text-white shadow-xl shadow-slate-950/20 md:grid-cols-[minmax(0,0.65fr)_minmax(320px,1fr)] md:items-center">
        <div class="grid gap-2">
            <p class="text-xs font-bold uppercase tracking-normal text-teal-300">Project context</p>
            <h2 class="text-2xl font-bold">CS85 work stays connected to the portfolio.</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach ($badges as $badge)
                <span class="rounded-lg border border-white/10 bg-white/10 px-3 py-2 text-xs font-bold text-slate-200">{{ $badge }}</span>
            @endforeach
        </div>
    </section>
@endsection
