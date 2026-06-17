@extends('layouts.app', ['title' => 'Starter Stack - CS85'])

@section('content')
    <section class="max-w-3xl py-3">
        <p class="mb-3 text-xs font-bold uppercase tracking-normal text-orange-700">Starter Stack</p>
        <h1 class="mb-4 max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">Tools selected for coursework now and extension later.</h1>
        <p class="text-lg leading-8 text-slate-600">
            The stack stays simple enough for the early modules while keeping a clean path toward
            CRUD features, admin workflows, and the AI-powered final project.
        </p>
    </section>

    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-4" aria-label="Installed stack groups">
        @foreach ($stack as $group)
            <article class="grid min-h-44 content-start gap-3 rounded-lg border border-stone-300 bg-white p-6">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-500">{{ $group['category'] }}</span>
                <ul class="grid gap-2 pl-5 leading-7 text-slate-600">
                    @foreach ($group['items'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </article>
        @endforeach
    </section>
@endsection
