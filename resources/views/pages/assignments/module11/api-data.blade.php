@extends('layouts.app', [
    'title' => 'Assignment 11A: API Data - CS85',
    'description' => 'A resilient Laravel API consumer that normalizes JSONPlaceholder data through clean application boundaries.',
])

@section('content')
    <nav class="sticky top-2 z-20 overflow-x-auto rounded-lg border border-stone-300 bg-stone-50/95 p-3 shadow-xl shadow-slate-900/10 backdrop-blur" aria-label="Roadmap module switcher">
        <div class="flex min-w-max gap-2 text-sm font-bold">
            <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-center text-teal-800 no-underline transition hover:border-teal-700" href="{{ route('roadmap') }}">Roadmap</a>
            @foreach ($modules as $roadmapModule)
                <a
                    class="inline-flex min-h-11 items-center justify-center whitespace-nowrap rounded-lg border px-3 py-2 text-center text-sm font-bold no-underline transition {{ $roadmapModule['slug'] === $module['slug'] ? 'border-teal-600 bg-slate-950 text-white shadow-lg shadow-slate-900/15' : 'border-stone-300 bg-white text-slate-600 hover:border-teal-700 hover:text-teal-800' }}"
                    href="{{ route('roadmap.module', $roadmapModule['slug']) }}"
                    @if ($roadmapModule['slug'] === $module['slug']) aria-current="page" @endif
                >
                    {{ $roadmapModule['module'] }}
                </a>
            @endforeach
        </div>
    </nav>

    @include('pages.assignments.module11.track-navigation')

    <section class="overflow-hidden rounded-lg border border-teal-800 bg-slate-950 text-white shadow-2xl shadow-teal-950/20">
        <div class="grid gap-8 bg-[radial-gradient(circle_at_top_right,_rgba(13,148,136,.32),_transparent_42%)] px-6 py-10 lg:grid-cols-[minmax(0,1.5fr)_minmax(18rem,.7fr)] lg:items-center lg:px-9 lg:py-14">
            <div class="grid gap-6">
                <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-normal text-slate-300">
                    <span class="rounded-full bg-teal-400/15 px-3 py-1.5 text-teal-200 ring-1 ring-teal-300/25">Module 11</span>
                    <span>Advanced implementation</span>
                    <span class="text-teal-300">/</span>
                    <span class="text-emerald-300">Complete</span>
                </div>

                <div class="grid gap-3">
                    <p class="text-sm font-bold uppercase tracking-normal text-orange-300">API Data</p>
                    <h1 class="max-w-4xl text-4xl font-black leading-none tracking-tight sm:text-5xl lg:text-6xl">Turn remote JSON into trusted application data.</h1>
                    <p class="max-w-3xl text-base leading-7 text-slate-300 md:text-lg md:leading-8">
                        Laravel fetches users from JSONPlaceholder, validates the response shape, maps records into typed objects, and gives the interface a stable contract for search, sorting, caching, and JSON output.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Laravel HTTP client</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Typed DTO</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Validated query</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">Cache + fallback</span>
                    <span class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">JSON endpoint</span>
                </div>
            </div>

            <aside class="rounded-2xl border border-white/15 bg-white/10 p-6 backdrop-blur" aria-label="API response status">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-xs font-bold uppercase tracking-normal text-teal-200">Response status</p>
                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $apiResult->degraded ? 'bg-orange-300/15 text-orange-200' : 'bg-emerald-300/15 text-emerald-200' }}">
                        <span class="h-2 w-2 rounded-full {{ $apiResult->degraded ? 'bg-orange-300' : 'bg-emerald-300' }}"></span>
                        {{ $apiResult->degraded ? 'Fallback active' : 'API ready' }}
                    </span>
                </div>
                <p class="mt-4 text-3xl font-black">{{ ucfirst($apiResult->source) }} data</p>
                <p class="mt-3 leading-7 text-slate-300">{{ $apiResult->message }}</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a class="rounded-lg bg-white px-4 py-3 text-sm font-bold text-slate-950 no-underline transition hover:bg-teal-100" href="{{ route('assignments.module11a.index', [...$filters, 'refresh' => 1]) }}">Fetch fresh data</a>
                    <a class="rounded-lg border border-white/20 px-4 py-3 text-sm font-bold text-white no-underline transition hover:bg-white/10" href="{{ route('assignments.module11a.data', $filters) }}">View JSON</a>
                </div>
            </aside>
        </div>

        <div class="grid border-t border-white/10 sm:grid-cols-2 lg:grid-cols-4">
            <div class="border-b border-white/10 p-5 sm:border-r lg:border-b-0">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Validated records</span>
                <strong class="mt-2 block text-3xl">{{ $dataset['total'] }}</strong>
                <span class="text-sm text-slate-400">Ready for the application</span>
            </div>
            <div class="border-b border-white/10 p-5 lg:border-b-0 lg:border-r">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Companies</span>
                <strong class="mt-2 block text-3xl">{{ $dataset['companies'] }}</strong>
                <span class="text-sm text-slate-400">Normalized nested values</span>
            </div>
            <div class="border-b border-white/10 p-5 sm:border-r sm:border-b-0">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Cities</span>
                <strong class="mt-2 block text-3xl">{{ $dataset['cities'] }}</strong>
                <span class="text-sm text-slate-400">Safe filterable strings</span>
            </div>
            <div class="p-5">
                <span class="text-xs font-bold uppercase tracking-normal text-slate-400">Cache window</span>
                <strong class="mt-2 block text-3xl">10m</strong>
                <span class="text-sm text-slate-400">Fewer remote requests</span>
            </div>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-[minmax(18rem,.72fr)_minmax(0,1.6fr)]" aria-label="API data workbench">
        <aside class="grid content-start gap-5 rounded-2xl border border-teal-200 bg-teal-50/70 p-5 shadow-xl shadow-teal-950/5 lg:sticky lg:top-24 lg:self-start">
            <div>
                <p class="text-xs font-black uppercase tracking-normal text-teal-800">GET · Explore response</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Query the DTOs</h2>
                <p class="mt-3 leading-7 text-slate-600">These controls filter normalized objects in the application layer. The external endpoint is fixed on the server.</p>
            </div>

            <form class="grid gap-4" action="{{ route('assignments.module11a.index') }}" method="GET">
                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Search
                    <input class="min-h-11 rounded-lg border border-stone-300 bg-white px-3 text-slate-950 outline-none transition focus:border-teal-700 focus:ring-2 focus:ring-teal-100" name="search" type="search" value="{{ $filters['search'] }}" placeholder="Name, email, city, company">
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Sort
                    <select class="min-h-11 rounded-lg border border-stone-300 bg-white px-3 text-slate-950 outline-none transition focus:border-teal-700 focus:ring-2 focus:ring-teal-100" name="sort">
                        @foreach ($sorts as $value => $label)
                            <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Result limit
                    <select class="min-h-11 rounded-lg border border-stone-300 bg-white px-3 text-slate-950 outline-none transition focus:border-teal-700 focus:ring-2 focus:ring-teal-100" name="limit">
                        @foreach ([4, 6, 10] as $limit)
                            <option value="{{ $limit }}" @selected($filters['limit'] === $limit)>{{ $limit }} records</option>
                        @endforeach
                    </select>
                </label>

                <button class="min-h-11 rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-teal-800" type="submit">Run query</button>
                <a class="text-center text-sm font-bold text-teal-800" href="{{ route('assignments.module11a.index') }}">Clear filters</a>
            </form>

            <div class="rounded-xl border border-teal-200 bg-white p-4">
                <p class="text-xs font-black uppercase tracking-normal text-teal-800">Why no URL field?</p>
                <p class="mt-2 text-sm leading-6 text-slate-600">Letting a visitor choose the server-side request URL would create an SSRF risk. The provider endpoint belongs in trusted configuration.</p>
            </div>
        </aside>

        <div class="overflow-hidden rounded-2xl border border-stone-300 bg-white shadow-xl shadow-slate-900/5">
            <div class="flex flex-col gap-3 border-b border-stone-200 bg-stone-50 p-5 sm:flex-row sm:items-end sm:justify-between sm:p-6">
                <div>
                    <p class="text-xs font-black uppercase tracking-normal text-teal-800">Normalized result</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight">API contacts</h2>
                </div>
                <span class="w-fit rounded-full bg-teal-100 px-4 py-2 text-sm font-black text-teal-950">{{ $dataset['returned'] }} returned</span>
            </div>

            @if ($dataset['contacts'] === [])
                <div class="m-5 rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-10 text-center sm:m-6">
                    <h3 class="text-xl font-black">No API contacts found</h3>
                    <p class="mt-2 leading-7 text-slate-600">Try a broader name, email, company, or city search.</p>
                </div>
            @else
                <div class="grid gap-4 p-5 md:grid-cols-2 sm:p-6">
                    @foreach ($dataset['contacts'] as $contact)
                        @php
                            $initials = collect(explode(' ', $contact->name))
                                ->filter()
                                ->take(2)
                                ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
                                ->implode('');
                        @endphp
                        <article class="group grid gap-4 rounded-2xl border border-stone-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-teal-400 hover:shadow-lg hover:shadow-teal-950/5">
                            <div class="flex items-start gap-4">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-slate-950 text-sm font-black text-white">{{ $initials }}</span>
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-black text-slate-950">{{ $contact->name }}</h3>
                                    <p class="truncate text-sm font-bold text-teal-800">{{ '@'.$contact->username }}</p>
                                </div>
                                <span class="ml-auto rounded-lg bg-slate-100 px-2 py-1 font-mono text-xs font-black text-slate-600">#{{ $contact->id }}</span>
                            </div>

                            <dl class="grid gap-3 text-sm">
                                <div class="grid grid-cols-[4.5rem_1fr] gap-3">
                                    <dt class="font-bold text-slate-500">Company</dt>
                                    <dd class="font-semibold text-slate-800">{{ $contact->company }}</dd>
                                </div>
                                <div class="grid grid-cols-[4.5rem_1fr] gap-3">
                                    <dt class="font-bold text-slate-500">City</dt>
                                    <dd class="font-semibold text-slate-800">{{ $contact->city }}</dd>
                                </div>
                                <div class="grid grid-cols-[4.5rem_1fr] gap-3">
                                    <dt class="font-bold text-slate-500">Email</dt>
                                    <dd class="min-w-0 truncate"><a class="font-semibold text-sky-800" href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></dd>
                                </div>
                            </dl>

                            <div class="flex flex-wrap gap-2 border-t border-stone-100 pt-4 text-xs font-bold">
                                <span class="rounded-lg bg-orange-50 px-2.5 py-1.5 text-orange-800">{{ $contact->phone }}</span>
                                <a class="rounded-lg bg-teal-50 px-2.5 py-1.5 text-teal-800 no-underline" href="https://{{ $contact->website }}" rel="noreferrer" target="_blank">{{ $contact->website }} ↗</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="rounded-2xl border border-stone-300 bg-white p-6 shadow-xl shadow-slate-900/5 md:p-8" aria-labelledby="pipeline-title">
        <div class="grid gap-3 lg:grid-cols-[1fr_auto] lg:items-end">
            <div>
                <p class="text-xs font-black uppercase tracking-normal text-teal-800">Request lifecycle</p>
                <h2 id="pipeline-title" class="mt-2 text-3xl font-black tracking-tight text-slate-950">How the API data reaches Blade</h2>
            </div>
            <p class="max-w-xl leading-7 text-slate-600">Each boundary reduces uncertainty before third-party data is allowed into the view.</p>
        </div>

        <ol class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['01', 'Request', 'A validated GET request accepts only known search, sort, limit, and refresh values.'],
                ['02', 'Fetch', 'The infrastructure client calls one configured HTTPS endpoint with JSON headers and a short timeout.'],
                ['03', 'Normalize', 'ApiContact rejects incomplete records and exposes a stable typed shape.'],
                ['04', 'Transform', 'The application service searches, sorts, limits, and calculates dataset statistics.'],
                ['05', 'Present', 'Blade renders escaped values, while the JSON route returns the same trusted contract.'],
            ] as [$number, $title, $copy])
                <li class="grid content-start gap-3 rounded-xl border border-stone-200 bg-stone-50 p-5">
                    <span class="grid h-10 w-10 place-items-center rounded-lg {{ $number === '05' ? 'bg-teal-700' : 'bg-slate-950' }} font-black text-white">{{ $number }}</span>
                    <strong class="text-lg text-slate-950">{{ $title }}</strong>
                    <p class="text-sm leading-6 text-slate-600">{{ $copy }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    <section aria-labelledby="boundaries-title">
        <p class="text-xs font-black uppercase tracking-normal text-orange-700">Clean Architecture map</p>
        <h2 id="boundaries-title" class="mt-2 text-3xl font-black tracking-tight text-slate-950">Four responsibilities, four clear owners</h2>
        <p class="mt-3 max-w-3xl leading-7 text-slate-600">The API provider can change without forcing HTTP details into the controller, filter rules into Blade, or framework helpers into the domain object.</p>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <article class="rounded-2xl border border-sky-200 bg-sky-50/70 p-6">
                <span class="rounded-lg bg-sky-700 px-3 py-2 text-xs font-black text-white">P</span>
                <p class="mt-5 text-xs font-black uppercase tracking-normal text-sky-800">Presentation</p>
                <h3 class="mt-2 text-2xl font-black">Module11AApiDataController</h3>
                <p class="mt-3 leading-7 text-slate-600">Coordinates the use case and chooses HTML or JSON without knowing remote-response details.</p>
            </article>
            <article class="rounded-2xl border border-violet-200 bg-violet-50/70 p-6">
                <span class="rounded-lg bg-violet-700 px-3 py-2 text-xs font-black text-white">A</span>
                <p class="mt-5 text-xs font-black uppercase tracking-normal text-violet-800">Application</p>
                <h3 class="mt-2 text-2xl font-black">BrowseApiContacts</h3>
                <p class="mt-3 leading-7 text-slate-600">Owns search, deterministic sorting, result limits, and summary statistics.</p>
            </article>
            <article class="rounded-2xl border border-teal-200 bg-teal-50/70 p-6">
                <span class="rounded-lg bg-teal-700 px-3 py-2 text-xs font-black text-white">D</span>
                <p class="mt-5 text-xs font-black uppercase tracking-normal text-teal-800">Domain</p>
                <h3 class="mt-2 text-2xl font-black">ApiContact</h3>
                <p class="mt-3 leading-7 text-slate-600">Turns an untrusted nested payload into the exact fields this application promises to use.</p>
            </article>
            <article class="rounded-2xl border border-orange-200 bg-orange-50/70 p-6">
                <span class="rounded-lg bg-orange-700 px-3 py-2 text-xs font-black text-white">I</span>
                <p class="mt-5 text-xs font-black uppercase tracking-normal text-orange-800">Infrastructure</p>
                <h3 class="mt-2 text-2xl font-black">JsonPlaceholderUserClient</h3>
                <p class="mt-3 leading-7 text-slate-600">Owns the HTTPS call, timeout, response validation, cache, and versioned offline fallback.</p>
            </article>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-stone-300 bg-white shadow-xl shadow-slate-900/5" aria-labelledby="evidence-title">
        <div class="p-6 md:p-8">
            <p class="text-xs font-black uppercase tracking-normal text-teal-800">Implementation evidence</p>
            <h2 id="evidence-title" class="mt-2 text-3xl font-black tracking-tight">Where each requirement lives</h2>
            <p class="mt-3 leading-7 text-slate-600">The assignment is backed by runtime code and focused feature tests, not only explanatory copy.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-3xl border-collapse text-left text-sm">
                <thead class="bg-slate-950 text-white">
                    <tr>
                        <th class="px-5 py-4 text-xs font-black uppercase tracking-normal" scope="col">Behavior</th>
                        <th class="px-5 py-4 text-xs font-black uppercase tracking-normal" scope="col">Implementation</th>
                        <th class="px-5 py-4 text-xs font-black uppercase tracking-normal" scope="col">Proof</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @foreach ([
                        ['Consume remote API', 'JsonPlaceholderUserClient::fetch', 'HTTP fake verifies the configured endpoint and normalized response.'],
                        ['Validate query', 'BrowseModule11ApiDataRequest', 'Unknown sorts and unsafe limits return validation errors.'],
                        ['Normalize JSON', 'ApiContact::fromPayload', 'Incomplete records never reach the UI or JSON contract.'],
                        ['Search and sort', 'BrowseApiContacts::handle', 'Feature tests assert deterministic filtered output.'],
                        ['Stay resilient', 'Laravel cache + users.json fallback', 'Failure tests prove the page still returns a usable dataset.'],
                        ['Expose JSON', 'Module11AApiDataController::data', 'The endpoint returns meta, filters, and typed contact data.'],
                    ] as [$behavior, $implementation, $proof])
                        <tr class="align-top">
                            <th class="px-5 py-4 font-black text-slate-950" scope="row">{{ $behavior }}</th>
                            <td class="px-5 py-4 font-mono text-xs font-bold text-teal-800">{{ $implementation }}</td>
                            <td class="px-5 py-4 leading-6 text-slate-600">{{ $proof }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid gap-6 rounded-2xl border border-teal-200 bg-gradient-to-br from-teal-50 to-white p-6 shadow-xl shadow-teal-950/5 lg:grid-cols-[1fr_auto] lg:items-center md:p-8" aria-labelledby="reflection-title">
        <div>
            <p class="text-xs font-black uppercase tracking-normal text-teal-800">Assignment reflection</p>
            <h2 id="reflection-title" class="mt-2 text-3xl font-black tracking-tight">An API response is input, not a domain model.</h2>
            <p class="mt-4 max-w-4xl leading-7 text-slate-600">
                The key lesson is to distrust the provider shape at the edge. Once Laravel validates and normalizes the payload, the rest of the application can work with a predictable contract. Caching protects the provider, a fallback protects the page, and a fixed configured endpoint protects the server.
            </p>
        </div>
        <div class="flex flex-wrap gap-3 lg:justify-end">
            <a class="rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white no-underline transition hover:bg-teal-800" href="{{ route('assignments.module11a.data', $filters) }}">Inspect JSON response</a>
            <a class="rounded-lg border border-teal-300 bg-white px-4 py-3 text-sm font-black text-teal-900 no-underline transition hover:border-teal-700" href="{{ route('roadmap') }}">Back to roadmap</a>
        </div>
    </section>
@endsection
