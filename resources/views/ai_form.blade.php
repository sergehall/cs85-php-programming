@extends('layouts.app', [
    'title' => 'Assignment 12A: Integrating OpenAI - CS85',
    'description' => 'A verified Laravel integration with the OpenAI API and gpt-4o-mini, featuring adaptive prompts, validation, and editable output.',
])

@section('content')
    <nav class="sticky top-2 z-20 overflow-x-auto rounded-lg border border-stone-300 bg-stone-50/95 p-3 shadow-xl shadow-slate-900/10 backdrop-blur" aria-label="Roadmap module switcher">
        <div class="flex min-w-max gap-2 text-sm font-bold">
            <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-center text-orange-800 no-underline transition hover:border-orange-700" href="{{ route('roadmap') }}">Roadmap</a>
            @foreach ($modules as $roadmapModule)
                <a
                    class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-lg border px-3 py-2 text-center text-sm font-bold no-underline transition {{ $roadmapModule['slug'] === $module['slug'] ? 'border-orange-600 bg-slate-950 text-white shadow-lg shadow-slate-900/15' : 'border-stone-300 bg-white text-slate-600 hover:border-orange-700 hover:text-orange-800' }}"
                    href="{{ route('roadmap.module', $roadmapModule['slug']) }}"
                    @if ($roadmapModule['slug'] === $module['slug']) aria-current="page" @endif
                >
                    {{ $roadmapModule['module'] }}
                </a>
            @endforeach
        </div>
    </nav>

    @include('pages.assignments.module12.track-navigation')

    <section class="overflow-hidden rounded-lg border border-orange-800 bg-slate-950 text-white shadow-2xl shadow-orange-950/20">
        <div class="grid gap-8 bg-[radial-gradient(circle_at_top_right,_rgba(249,115,22,.34),_transparent_44%)] px-6 py-10 lg:grid-cols-[minmax(0,1.45fr)_minmax(18rem,.75fr)] lg:items-center lg:px-9 lg:py-14">
            <div class="grid gap-6">
                <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-normal text-slate-300">
                    <span class="rounded-full bg-orange-400/15 px-3 py-1.5 text-orange-200 ring-1 ring-orange-300/25">Module 12</span>
                    <span>Assignment 12A</span>
                    <span class="text-orange-300">/</span>
                    <span class="text-emerald-300">Complete</span>
                    <span class="text-orange-300">/</span>
                    <span class="text-emerald-300">OpenAI API connected</span>
                </div>

                <div class="grid gap-3">
                    <p class="text-sm font-bold uppercase tracking-normal text-orange-300">Integrating OpenAI</p>
                    <h1 class="max-w-4xl text-4xl font-black leading-none tracking-tight sm:text-5xl lg:text-6xl">Generate an editable draft from one structured prompt.</h1>
                    <p class="max-w-3xl text-base leading-7 text-slate-300 md:text-lg md:leading-8">
                        Laravel validates a title, content type, and tone, builds an adaptive prompt in a service class, and calls the OpenAI Chat Completions API with <strong class="text-white">gpt-4o-mini</strong>.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">OpenAI Chat Completions</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">gpt-4o-mini</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Adaptive prompt</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Server-side API key</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Fake-backed tests</span>
                </div>
            </div>

            <aside class="rounded-2xl border border-white/15 bg-white/10 p-6 backdrop-blur" aria-label="Assignment request flow">
                <p class="text-xs font-bold uppercase tracking-normal text-orange-200">Request flow</p>
                <ol class="mt-5 grid gap-4 text-sm">
                    @foreach ([
                        ['01', 'Validate', 'Title, type, and tone must match the form contract.'],
                        ['02', 'Prompt', 'The service selects a role, task, length, and format.'],
                        ['03', 'Generate', 'Laravel calls OpenAI; the browser never receives the key.'],
                        ['04', 'Edit', 'The returned draft stays editable before publication.'],
                    ] as [$number, $title, $copy])
                        <li class="grid grid-cols-[2.25rem_1fr] gap-3">
                            <span class="grid h-9 w-9 place-items-center rounded-lg bg-orange-300 font-black text-slate-950">{{ $number }}</span>
                            <span><strong class="block text-white">{{ $title }}</strong><span class="mt-1 block leading-5 text-slate-300">{{ $copy }}</span></span>
                        </li>
                    @endforeach
                </ol>
            </aside>
        </div>
    </section>

    <section class="rounded-2xl border border-emerald-300 bg-emerald-50 p-5 shadow-xl shadow-emerald-950/5 md:p-7" aria-labelledby="openai-api-status">
        <div class="grid gap-5 lg:grid-cols-[minmax(0,1.2fr)_minmax(18rem,.8fr)] lg:items-center">
            <div>
                <p class="text-xs font-black uppercase tracking-normal text-emerald-800">Live integration verified</p>
                <h2 id="openai-api-status" class="mt-2 text-3xl font-black tracking-tight text-slate-950">Assignment 12A is connected to the OpenAI API.</h2>
                <p class="mt-3 max-w-4xl leading-7 text-slate-700">
                    Laravel successfully sends the validated prompt to OpenAI, uses <strong>gpt-4o-mini</strong> to generate the response, and returns the result to this page as editable content.
                </p>
            </div>

            <dl class="grid gap-3 text-sm sm:grid-cols-3 lg:grid-cols-1">
                @foreach ([
                    ['Provider', 'OpenAI API'],
                    ['Endpoint', '/v1/chat/completions'],
                    ['Secret boundary', 'Server-side .env only'],
                ] as [$label, $value])
                    <div class="rounded-xl border border-emerald-200 bg-white p-4">
                        <dt class="font-black text-slate-950">{{ $label }}</dt>
                        <dd class="mt-1 font-mono text-xs text-emerald-900">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-[minmax(19rem,.8fr)_minmax(0,1.2fr)]" aria-label="AI content generator">
        <article class="rounded-2xl border border-orange-200 bg-orange-50/70 p-5 shadow-xl shadow-orange-950/5 md:p-7">
            <p class="text-xs font-black uppercase tracking-normal text-orange-800">POST · /ai-generate</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Create a draft</h2>
            <p class="mt-3 leading-7 text-slate-600">Try the same topic with different formats and tones to see how prompt constraints change the result.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                    <strong class="block font-black">The draft was not generated.</strong>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="mt-6 grid gap-5" method="POST" action="{{ route('ai.generate') }}">
                @csrf

                <label class="grid gap-2 text-sm font-bold text-slate-800" for="title">
                    Title or topic
                    <input
                        class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 text-slate-950 outline-none transition focus:border-orange-700 focus:ring-2 focus:ring-orange-100"
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title', $title ?? '') }}"
                        minlength="5"
                        maxlength="255"
                        placeholder="Why Laravel services improve testability"
                        required
                    >
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-800" for="type">
                    Content type
                    <select class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 text-slate-950 outline-none transition focus:border-orange-700 focus:ring-2 focus:ring-orange-100" id="type" name="type">
                        @foreach ([
                            'blog post' => 'Blog post',
                            'meta description' => 'Meta description',
                            'email subject line' => 'Email subject line',
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $selectedType ?? 'blog post') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-800" for="tone">
                    Tone
                    <select class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 text-slate-950 outline-none transition focus:border-orange-700 focus:ring-2 focus:ring-orange-100" id="tone" name="tone">
                        @foreach ([
                            'professional' => 'Professional',
                            'casual' => 'Casual',
                            'humorous' => 'Humorous',
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(old('tone', $selectedTone ?? 'professional') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <button class="min-h-12 rounded-xl bg-slate-950 px-5 py-3 font-black text-white transition hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2" type="submit">
                    Generate with gpt-4o-mini
                </button>
            </form>
        </article>

        <article class="overflow-hidden rounded-2xl border border-stone-300 bg-white shadow-xl shadow-slate-900/5">
            <div class="border-b border-stone-200 bg-stone-50 p-5 md:p-6">
                <p class="text-xs font-black uppercase tracking-normal text-orange-800">Editable result</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight">Generated draft</h2>
                <p class="mt-2 leading-6 text-slate-600">AI output is a starting point. Review facts, improve the voice, and edit before publishing.</p>
            </div>

            <div class="p-5 md:p-6">
                @isset($output)
                    <label class="sr-only" for="generated-draft">Generated draft</label>
                    <textarea
                        class="min-h-96 w-full resize-y rounded-xl border border-orange-200 bg-orange-50/40 p-4 font-mono text-sm leading-7 text-slate-900 outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-100"
                        id="generated-draft"
                        spellcheck="true"
                    >{{ $output }}</textarea>
                @else
                    <div class="grid min-h-96 place-items-center rounded-xl border border-dashed border-stone-300 bg-stone-50 p-8 text-center">
                        <div class="max-w-md">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-orange-100 text-2xl text-orange-800" aria-hidden="true">✦</span>
                            <h3 class="mt-5 text-xl font-black text-slate-950">Your draft will appear here</h3>
                            <p class="mt-2 leading-7 text-slate-600">Submit the form to call the service. Automated tests replace OpenAI with a fake, so the test suite never spends API credits.</p>
                        </div>
                    </div>
                @endisset
            </div>
        </article>
    </section>

    <section class="grid gap-5 md:grid-cols-2">
        <article class="rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-8">
            <p class="text-xs font-black uppercase tracking-normal text-orange-800">Rubric evidence</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight">Where each requirement lives</h2>
            <dl class="mt-6 grid gap-4 text-sm">
                @foreach ([
                    ['Form and editable output', 'resources/views/ai_form.blade.php'],
                    ['Validation and coordination', 'app/Http/Controllers/AiContentController.php'],
                    ['Prompt and API request', 'app/Services/AiContentService.php'],
                    ['Secret and model configuration', '.env + config/services.php'],
                    ['Fake service proof', 'tests/Feature/Module12AiContentAssignmentTest.php'],
                ] as [$label, $path])
                    <div class="rounded-xl border border-stone-200 bg-stone-50 p-4">
                        <dt class="font-black text-slate-950">{{ $label }}</dt>
                        <dd class="mt-2 break-all font-mono text-xs leading-5 text-orange-900">{{ $path }}</dd>
                    </div>
                @endforeach
            </dl>
        </article>

        <article class="rounded-2xl border border-orange-800 bg-slate-950 p-6 text-white shadow-xl shadow-orange-950/15 md:p-8">
            <span class="rounded-full bg-orange-400/15 px-3 py-1.5 text-xs font-black uppercase tracking-normal text-orange-200 ring-1 ring-orange-300/25">Final project extension</span>
            <h2 class="mt-5 text-3xl font-black tracking-tight">The same boundary, expanded professionally</h2>
            <p class="mt-4 leading-7 text-slate-300">
                Assignment 12A proves one secure request. The final project keeps AI behind Laravel, then adds provider abstraction, authenticated multi-turn history, model routing, streaming, tools, telemetry, retry behavior, and sanitized Markdown.
            </p>
            <a class="mt-6 inline-flex rounded-lg bg-white px-4 py-3 text-sm font-black text-slate-950 no-underline transition hover:bg-orange-100" href="{{ route('roadmap.module', 'module-12') }}">Inspect the Final Project</a>
        </article>
    </section>

    <section class="rounded-2xl border border-orange-200 bg-gradient-to-br from-orange-50 to-white p-6 shadow-xl shadow-orange-950/5 md:p-8" aria-labelledby="module12a-reflection">
        <p class="text-xs font-black uppercase tracking-normal text-orange-800">Assignment reflection</p>
        <h2 id="module12a-reflection" class="mt-2 text-3xl font-black tracking-tight">Prompt constraints turn one model into three focused writing tools.</h2>
        <div class="mt-5 grid gap-5 text-sm leading-7 text-slate-600 md:grid-cols-3">
            <p><strong class="block text-slate-950">Tone and role</strong>Professional, casual, and humorous roles change vocabulary, rhythm, and the amount of personality in the response.</p>
            <p><strong class="block text-slate-950">Content type</strong>The blog prompt requests sections and depth, while metadata and subject prompts enforce one-line character or word limits.</p>
            <p><strong class="block text-slate-950">Production next step</strong>Add authenticated quotas, usage analytics, caching where semantics allow it, provider abstraction, streaming, and structured evaluations.</p>
        </div>
    </section>
@endsection
