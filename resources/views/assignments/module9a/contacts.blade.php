@extends('layouts.app', [
    'title' => 'Module 9 Assignment 9A: Contact List App - CS85',
    'description' => 'An interactive Laravel CRUD workbench for importing, filtering, creating, updating, and deleting contact records.',
])

@section('content')
    @if (session('status'))
        <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-5 py-4 font-bold text-emerald-900" role="status">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-orange-300 bg-orange-50 p-5 text-orange-950" role="alert">
            <p class="font-black">The database operation was not completed.</p>
            <ul class="mt-3 list-disc space-y-1 pl-5 text-sm leading-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="relative isolate overflow-hidden rounded-3xl border border-amber-700 bg-slate-950 text-white shadow-2xl shadow-amber-950/15">
        <div class="absolute -top-40 right-0 -z-10 h-96 w-96 rounded-full bg-amber-500/20 blur-3xl" aria-hidden="true"></div>
        <div class="grid gap-8 px-6 py-9 sm:px-8 lg:grid-cols-[1.25fr_.75fr] lg:px-11 lg:py-12">
            <div class="grid content-start gap-5">
                <a class="w-fit rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold text-white no-underline transition hover:bg-white/15" href="{{ route('roadmap.module', 'module-9') }}">← Return to Module 9</a>
                <div class="grid gap-3">
                    <p class="text-xs font-black uppercase tracking-[.16em] text-amber-300">Module 9 · Assignment 9A</p>
                    <h1 class="max-w-4xl text-4xl leading-none font-black tracking-tight sm:text-6xl">Contact List CRUD workbench</h1>
                    <p class="max-w-3xl text-base leading-7 text-slate-300 sm:text-lg sm:leading-8">
                        Load a default JSON dataset, inspect the database with GET filters, and run Create, Read, Update, and Delete operations from one Laravel Blade interface.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="rounded-lg bg-white/8 px-3 py-2 ring-1 ring-white/15">Eloquent ORM</span>
                    <span class="rounded-lg bg-white/8 px-3 py-2 ring-1 ring-white/15">Two related tables</span>
                    <span class="rounded-lg bg-white/8 px-3 py-2 ring-1 ring-white/15">Form Request validation</span>
                    <span class="rounded-lg bg-white/8 px-3 py-2 ring-1 ring-white/15">JSON import</span>
                </div>
            </div>

            <aside class="grid content-start gap-4 rounded-2xl border border-white/15 bg-white/8 p-5" aria-label="CRUD request map">
                <p class="text-xs font-black uppercase tracking-[.14em] text-amber-300">HTTP methods in this lab</p>
                <div class="grid gap-2 text-sm">
                    @foreach ([
                        ['GET', 'Read and filter contacts', 'bg-sky-300 text-sky-950'],
                        ['POST', 'Import JSON or create', 'bg-emerald-300 text-emerald-950'],
                        ['PUT', 'Update by contact ID', 'bg-violet-300 text-violet-950'],
                        ['DELETE', 'Remove one or clear all', 'bg-orange-300 text-orange-950'],
                    ] as [$method, $description, $classes])
                        <div class="grid grid-cols-[4.5rem_1fr] items-center gap-3 rounded-xl border border-white/10 bg-black/15 p-3">
                            <span class="rounded-lg px-2 py-1 text-center font-mono text-xs font-black {{ $classes }}">{{ $method }}</span>
                            <span class="font-bold text-slate-200">{{ $description }}</span>
                        </div>
                    @endforeach
                </div>
            </aside>
        </div>

        <div class="grid border-t border-white/10 bg-black/15 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Contacts', $stats['contacts'], 'Database rows'],
                ['Active', $stats['active'], 'Available records'],
                ['Admin samples', $stats['admins'], 'Educational role values'],
                ['Groups', $stats['groups'], 'One-to-many relation'],
            ] as [$label, $value, $description])
                <div class="border-b border-white/10 p-5 sm:border-r lg:border-b-0 last:border-r-0">
                    <span class="text-xs font-bold uppercase tracking-normal text-slate-400">{{ $label }}</span>
                    <strong class="mt-1 block text-3xl text-white">{{ $value }}</strong>
                    <span class="text-sm text-slate-300">{{ $description }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-[.85fr_1.15fr]" aria-labelledby="dataset-title">
        <article class="grid content-start gap-5 rounded-3xl border border-amber-200 bg-amber-50 p-6 shadow-xl shadow-amber-950/5">
            <div class="grid gap-2">
                <p class="text-xs font-black uppercase tracking-[.14em] text-amber-800">Step 1 · Seed data</p>
                <h2 id="dataset-title" class="text-3xl font-black tracking-tight text-slate-950">Default JSON dataset</h2>
                <p class="leading-7 text-slate-700">
                    The versioned file contains fictional contacts and group keys. Import is idempotent: existing emails are updated instead of duplicated.
                </p>
            </div>

            <code class="rounded-xl border border-amber-200 bg-white px-4 py-3 text-xs font-bold text-amber-900">{{ $sourceLabel }}</code>

            @if ($canMutate)
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                    <form method="POST" action="{{ route('assignments.module9a.contacts.dataset.store') }}">
                        @csrf
                        <button class="min-h-12 w-full rounded-xl bg-emerald-700 px-4 py-3 font-black text-white transition hover:bg-emerald-800" type="submit">POST · Import JSON</button>
                    </form>
                    <form method="POST" action="{{ route('assignments.module9a.contacts.dataset.destroy') }}" data-confirm="Clear every record from the Module 9 contacts and groups tables?">
                        @csrf
                        @method('DELETE')
                        <button class="min-h-12 w-full rounded-xl border border-orange-300 bg-white px-4 py-3 font-black text-orange-800 transition hover:border-orange-700 hover:bg-orange-50" type="submit">DELETE · Clear tables</button>
                    </form>
                </div>
            @else
                <div class="rounded-xl border border-violet-300 bg-violet-50 p-4 text-sm leading-6 text-violet-950">
                    Production writes require an authenticated administrator. The public page remains a read-only assignment demonstration.
                </div>
            @endif

            <p class="text-xs leading-5 text-slate-500">Use fictional training data only. These tables are intentionally separate from real application accounts.</p>
        </article>

        <details class="group overflow-hidden rounded-3xl border border-slate-700 bg-slate-950 text-white shadow-xl shadow-slate-900/10" open>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-black [&::-webkit-details-marker]:hidden">
                <span>JSON source preview</span>
                <span class="rounded-lg bg-white/10 px-3 py-1 text-xs text-slate-300 group-open:bg-amber-300 group-open:text-amber-950">Toggle</span>
            </summary>
            <pre class="max-h-112 overflow-auto border-t border-white/10 p-5 text-xs leading-6 text-slate-300"><code>{{ $sourceJson }}</code></pre>
        </details>
    </section>

    <section class="rounded-3xl border border-stone-300 bg-white p-5 shadow-xl shadow-slate-900/5 sm:p-7" aria-labelledby="get-title">
        <div class="flex flex-col gap-3 border-b border-stone-200 pb-5 md:flex-row md:items-end md:justify-between">
            <div class="grid gap-2">
                <p class="text-xs font-black uppercase tracking-[.14em] text-sky-700">Step 2 · Read</p>
                <h2 id="get-title" class="text-3xl font-black tracking-tight">GET query and filters</h2>
                <p class="max-w-3xl leading-7 text-slate-600">Every populated field becomes an allowlisted Eloquent condition. Empty fields are ignored.</p>
            </div>
            <a class="w-fit rounded-xl border border-sky-300 bg-sky-50 px-4 py-3 text-sm font-black text-sky-900 no-underline transition hover:border-sky-700" href="{{ route('assignments.module9a.contacts.data', array_filter($filters, fn ($value) => $value !== '' && $value !== null)) }}">GET · Open raw JSON</a>
        </div>

        <form class="mt-6 grid gap-5" method="GET" action="{{ route('assignments.module9a.contacts.index') }}">
            <div class="grid min-w-0 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    Search everything
                    <input class="min-h-12 min-w-0 rounded-xl border border-stone-300 px-4 py-3 font-normal" name="search" type="search" maxlength="120" value="{{ $filters['search'] }}" placeholder="name, email, phone...">
                </label>
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    First name
                    <input class="min-h-12 min-w-0 rounded-xl border border-stone-300 px-4 py-3 font-normal" name="first_name" type="search" maxlength="100" value="{{ $filters['first_name'] }}" placeholder="Maya">
                </label>
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    Last name
                    <input class="min-h-12 min-w-0 rounded-xl border border-stone-300 px-4 py-3 font-normal" name="last_name" type="search" maxlength="100" value="{{ $filters['last_name'] }}" placeholder="Chen">
                </label>
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    Email
                    <input class="min-h-12 min-w-0 rounded-xl border border-stone-300 px-4 py-3 font-normal" name="email" type="search" maxlength="255" value="{{ $filters['email'] }}" placeholder="@example.com">
                </label>
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    Phone
                    <input class="min-h-12 min-w-0 rounded-xl border border-stone-300 px-4 py-3 font-normal" name="phone" type="search" maxlength="32" value="{{ $filters['phone'] }}" placeholder="310">
                </label>
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    Group
                    <select class="min-h-12 min-w-0 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" name="group_id">
                        <option value="">All groups</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->getKey() }}" @selected($filters['group_id'] === $group->getKey())>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    Sample role
                    <select class="min-h-12 min-w-0 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" name="role">
                        <option value="">All roles</option>
                        @foreach ($roles as $roleValue => $roleLabel)
                            <option value="{{ $roleValue }}" @selected($filters['role'] === $roleValue)>{{ $roleLabel }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-2 text-sm font-black text-slate-700">
                    Status
                    <select class="min-h-12 min-w-0 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" name="status">
                        <option value="">Any status</option>
                        <option value="active" @selected($filters['status'] === 'active')>Active</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm font-black text-slate-700 sm:col-span-2 lg:col-span-1">
                    Sort
                    <select class="min-h-12 min-w-0 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" name="sort">
                        @foreach ($sorts as $sortValue => $sort)
                            <option value="{{ $sortValue }}" @selected($filters['sort'] === $sortValue)>{{ $sort['label'] }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="flex flex-wrap gap-3">
                <button class="min-h-12 rounded-xl bg-sky-700 px-5 py-3 font-black text-white transition hover:bg-sky-800" type="submit">GET · Run query</button>
                <a class="inline-flex min-h-12 items-center rounded-xl border border-stone-300 bg-white px-5 py-3 font-black text-slate-700 no-underline transition hover:border-sky-700 hover:text-sky-800" href="{{ route('assignments.module9a.contacts.index') }}">Clear filters</a>
            </div>
        </form>
    </section>

    <section class="grid gap-5 lg:grid-cols-[1.3fr_.7fr]" aria-labelledby="results-title">
        <article class="overflow-hidden rounded-3xl border border-stone-300 bg-white shadow-xl shadow-slate-900/5">
            <div class="flex flex-col gap-2 border-b border-stone-200 bg-stone-50 p-5 sm:flex-row sm:items-end sm:justify-between sm:p-6">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.14em] text-amber-800">Database result</p>
                    <h2 id="results-title" class="mt-2 text-3xl font-black tracking-tight">Contacts</h2>
                </div>
                <span class="w-fit rounded-full bg-amber-100 px-4 py-2 text-sm font-black text-amber-950">{{ $contacts->count() }} returned</span>
            </div>

            @if ($contacts->isEmpty())
                <div class="m-5 rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-8 text-center sm:m-6">
                    <h3 class="text-xl font-black">No contacts found</h3>
                    <p class="mt-2 leading-7 text-slate-600">Import the JSON dataset, create a record, or clear the current GET filters.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-5xl border-collapse text-left text-sm">
                        <thead class="bg-slate-950 text-white">
                            <tr>
                                <th class="px-4 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">ID</th>
                                <th class="px-4 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">Contact</th>
                                <th class="px-4 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">Phone / company</th>
                                <th class="px-4 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">Group</th>
                                <th class="px-4 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">Role / status</th>
                                <th class="px-4 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200">
                            @foreach ($contacts as $contact)
                                <tr class="align-top transition hover:bg-amber-50/60">
                                    <td class="px-4 py-4"><span class="inline-grid min-w-10 place-items-center rounded-lg bg-slate-100 px-2 py-1 font-mono font-black">{{ $contact->getKey() }}</span></td>
                                    <td class="px-4 py-4">
                                        <strong class="block text-base text-slate-950">{{ $contact->fullName() }}</strong>
                                        <a class="mt-1 block text-sky-800" href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                    </td>
                                    <td class="px-4 py-4 leading-6 text-slate-600">
                                        <span class="block">{{ $contact->phone ?: 'No phone' }}</span>
                                        <span class="block">{{ $contact->company ?: 'No company' }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $contact->group?->name ?? 'No group' }}</td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black {{ $contact->role === 'admin' ? 'bg-violet-100 text-violet-900' : 'bg-sky-100 text-sky-900' }}">{{ $roles[$contact->role] ?? $contact->role }}</span>
                                        <span class="mt-2 block text-xs font-bold {{ $contact->is_active ? 'text-emerald-700' : 'text-slate-500' }}">{{ $contact->is_active ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <a class="rounded-lg border border-violet-300 bg-violet-50 px-3 py-2 text-xs font-black text-violet-900 no-underline" href="{{ route('assignments.module9a.contacts.index', ['edit' => $contact->getKey()]) }}#update-contact">Edit</a>
                                            @if ($canMutate)
                                                <form method="POST" action="{{ route('assignments.module9a.contacts.destroy', $contact) }}" data-confirm="Delete contact #{{ $contact->getKey() }}?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg border border-orange-300 bg-orange-50 px-3 py-2 text-xs font-black text-orange-900" type="submit">Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </article>

        <details class="group overflow-hidden rounded-3xl border border-slate-700 bg-slate-950 text-white shadow-xl shadow-slate-900/10" open>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-black [&::-webkit-details-marker]:hidden">
                <span>Current GET response</span>
                <span class="rounded-lg bg-white/10 px-3 py-1 text-xs text-slate-300 group-open:bg-sky-300 group-open:text-sky-950">{{ $contacts->count() }} records</span>
            </summary>
            <pre class="max-h-160 overflow-auto border-t border-white/10 p-5 text-xs leading-6 text-slate-300"><code>{{ json_encode($jsonPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </details>
    </section>

    <section class="grid gap-5 lg:grid-cols-2" aria-label="Create and update operations">
        <article class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 shadow-xl shadow-emerald-950/5" id="create-contact">
            <div class="grid gap-2">
                <p class="text-xs font-black uppercase tracking-[.14em] text-emerald-800">Step 3 · Create</p>
                <h2 class="text-3xl font-black tracking-tight">POST a new contact</h2>
                <p class="leading-7 text-slate-700">The Form Request validates every field before Eloquent inserts a row.</p>
            </div>

            @if ($canMutate)
                <form class="mt-6 grid gap-5" method="POST" action="{{ route('assignments.module9a.contacts.store') }}">
                    @csrf
                    @include('assignments.module9a._contact-fields', ['contact' => null, 'prefix' => 'create'])
                    <button class="min-h-12 rounded-xl bg-emerald-700 px-5 py-3 font-black text-white transition hover:bg-emerald-800" type="submit">POST · Create contact</button>
                </form>
            @else
                <p class="mt-5 rounded-xl border border-violet-300 bg-white p-4 text-violet-950">Sign in as an administrator to mutate production data.</p>
            @endif
        </article>

        <article class="rounded-3xl border border-violet-200 bg-violet-50 p-6 shadow-xl shadow-violet-950/5" id="update-contact">
            <div class="grid gap-2">
                <p class="text-xs font-black uppercase tracking-[.14em] text-violet-800">Step 4 · Update</p>
                <h2 class="text-3xl font-black tracking-tight">PUT an existing contact</h2>
                <p class="leading-7 text-slate-700">Choose any training contact and update its phone, company, or group without changing the real users table.</p>
            </div>

            @if ($canMutate)
                <form class="mt-6 grid gap-5 rounded-2xl border border-violet-300 bg-white p-5" method="POST" action="{{ route('assignments.module9a.contacts.update-details') }}" data-contact-details-form>
                    @csrf
                    @method('PUT')

                    <label class="grid gap-2 text-sm font-black text-slate-700" for="update-contact-id">
                        Training contact
                        <select class="min-h-12 rounded-xl border border-violet-300 bg-white px-4 py-3 font-normal" id="update-contact-id" name="contact_id" required data-contact-details-select>
                            <option value="">Choose a contact…</option>
                            @foreach ($contactOptions as $contactOption)
                                <option
                                    value="{{ $contactOption->getKey() }}"
                                    data-phone="{{ $contactOption->phone }}"
                                    data-company="{{ $contactOption->company }}"
                                    data-group-id="{{ $contactOption->contact_group_id }}"
                                    @selected((string) old('contact_id', $editingContact?->getKey()) === (string) $contactOption->getKey())
                                >#{{ $contactOption->getKey() }} · {{ $contactOption->fullName() }} · {{ $contactOption->email }}</option>
                            @endforeach
                        </select>
                        <span class="text-xs leading-5 font-normal text-slate-500">The list contains every Module 9 training contact, even when the GET table is filtered.</span>
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="grid gap-2 text-sm font-black text-slate-700" for="update-details-phone">
                            Phone
                            <input class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="update-details-phone" name="details_phone" type="tel" maxlength="32" value="{{ old('details_phone', $editingContact?->phone) }}" placeholder="+1-310-555-0100" data-contact-details-phone>
                        </label>

                        <label class="grid gap-2 text-sm font-black text-slate-700" for="update-details-company">
                            Company
                            <input class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="update-details-company" name="details_company" type="text" maxlength="150" value="{{ old('details_company', $editingContact?->company) }}" placeholder="No company" data-contact-details-company>
                        </label>

                        <label class="grid gap-2 text-sm font-black text-slate-700 sm:col-span-2" for="update-details-group">
                            Contact group
                            <select class="min-h-12 rounded-xl border border-stone-300 bg-white px-4 py-3 font-normal" id="update-details-group" name="details_contact_group_id" data-contact-details-group>
                                <option value="">No group</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->getKey() }}" @selected((string) old('details_contact_group_id', $editingContact?->contact_group_id) === (string) $group->getKey())>{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <button class="min-h-12 rounded-xl bg-violet-700 px-5 py-3 font-black text-white transition hover:bg-violet-800 disabled:cursor-not-allowed disabled:opacity-50" type="submit" @disabled($contactOptions->isEmpty())>PUT · Update contact details</button>
                </form>

                <div class="mt-4 rounded-xl border border-violet-200 bg-white/70 p-4 text-sm leading-6 text-slate-700">
                    <strong class="text-violet-950">Training scope:</strong> local Module 9 can update any fictional contact. A real profile feature would authorize ownership so a regular user could update only their own record; production access to this lab remains administrator-only.
                </div>
            @else
                <p class="mt-5 rounded-xl border border-violet-300 bg-white p-4 text-violet-950">Production updates require an authenticated administrator.</p>
            @endif
        </article>
    </section>

    <section class="grid gap-5 rounded-3xl border border-orange-200 bg-orange-50 p-6 shadow-xl shadow-orange-950/5 md:grid-cols-[1fr_.8fr] md:items-start" aria-labelledby="delete-title">
        <div class="grid gap-3">
            <p class="text-xs font-black uppercase tracking-[.14em] text-orange-800">Step 5 · Delete</p>
            <h2 id="delete-title" class="text-3xl font-black tracking-tight">DELETE by unique ID</h2>
            <p class="max-w-2xl leading-7 text-slate-700">
                Enter the primary key shown in the GET result. After deletion, run GET again and the record will no longer be returned.
            </p>
            <p class="text-sm leading-6 text-slate-600">Database validation rejects missing IDs. This operation never touches the real <code>users</code> table.</p>
        </div>

        @if ($canMutate)
            <form class="grid gap-4 rounded-2xl border border-orange-300 bg-white p-5" method="POST" action="{{ route('assignments.module9a.contacts.destroy-by-id') }}" data-confirm="Delete the contact with this ID?">
                @csrf
                @method('DELETE')
                <label class="grid gap-2 text-sm font-black text-slate-700" for="delete-contact-id">
                    Contact ID
                    <input class="min-h-12 rounded-xl border border-stone-300 px-4 py-3 font-mono font-normal" id="delete-contact-id" name="contact_id" type="number" min="1" required placeholder="Example: 3">
                </label>
                <button class="min-h-12 rounded-xl bg-orange-700 px-5 py-3 font-black text-white transition hover:bg-orange-800" type="submit">DELETE · Remove by ID</button>
            </form>
        @endif
    </section>

    <section class="grid gap-4" aria-labelledby="evidence-title">
        <div class="grid gap-2">
            <p class="text-xs font-black uppercase tracking-[.14em] text-amber-800">Assignment evidence</p>
            <h2 id="evidence-title" class="text-3xl font-black tracking-tight">One UI, complete CRUD lifecycle</h2>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Create', 'POST', 'Validated form → Contact::create()', 'border-emerald-200 bg-emerald-50 text-emerald-900'],
                ['Read', 'GET', 'Allowlisted filters → Eloquent query', 'border-sky-200 bg-sky-50 text-sky-900'],
                ['Update', 'PUT', 'Route model binding → update()', 'border-violet-200 bg-violet-50 text-violet-900'],
                ['Delete', 'DELETE', 'Primary key → delete()', 'border-orange-200 bg-orange-50 text-orange-900'],
            ] as [$operation, $method, $description, $classes])
                <article class="grid content-start gap-3 rounded-2xl border p-5 {{ $classes }}">
                    <span class="w-fit rounded-lg bg-white px-2.5 py-1 font-mono text-xs font-black ring-1 ring-current/15">{{ $method }}</span>
                    <h3 class="text-xl font-black text-slate-950">{{ $operation }}</h3>
                    <p class="text-sm leading-6 text-slate-700">{{ $description }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection
