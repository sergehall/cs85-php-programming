@extends('layouts.app', [
    'title' => 'Contact List App - CS85',
    'description' => 'A conventional Laravel resource CRUD application for contacts.',
])

@section('content')
    <section class="grid gap-6">
        <div class="flex flex-col gap-4 rounded-3xl border border-sky-200 bg-sky-50 p-6 shadow-xl shadow-sky-950/5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[.14em] text-sky-800">Module 9 · Assignment 9A</p>
                <h1 class="mt-2 text-4xl font-black tracking-tight text-slate-950">Contact List App</h1>
                <p class="mt-3 max-w-2xl leading-7 text-slate-700">A standard Laravel resource controller with separate index, create, and edit Blade views.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a class="rounded-xl bg-sky-700 px-5 py-3 font-black text-white no-underline transition hover:bg-sky-800" href="{{ route('contacts.create') }}">Add contact</a>
                <a class="rounded-xl border border-slate-300 bg-white px-5 py-3 font-black text-slate-800 no-underline" href="{{ route('assignments.module9a.contacts.index') }}">Advanced workbench</a>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-5 py-4 font-bold text-emerald-900" role="status">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl border border-stone-300 bg-white shadow-xl shadow-slate-900/5">
            @if ($contacts->isEmpty())
                <div class="p-8 text-center">
                    <h2 class="text-2xl font-black text-slate-950">No contacts yet</h2>
                    <p class="mt-2 text-slate-600">Add the first contact to begin the list.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-3xl border-collapse text-left">
                        <thead class="bg-slate-950 text-white">
                            <tr>
                                <th class="px-5 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">Name</th>
                                <th class="px-5 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">Email</th>
                                <th class="px-5 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">Phone</th>
                                <th class="px-5 py-4 text-xs font-black uppercase tracking-[.1em]" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200">
                            @foreach ($contacts as $contact)
                                <tr class="hover:bg-sky-50/60">
                                    <td class="px-5 py-4 font-black text-slate-950">{{ $contact->name }}</td>
                                    <td class="px-5 py-4"><a class="text-sky-800" href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
                                    <td class="px-5 py-4 text-slate-700">{{ $contact->phone }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <a class="rounded-lg border border-violet-300 bg-violet-50 px-3 py-2 text-sm font-black text-violet-900 no-underline" href="{{ route('contacts.edit', $contact) }}">Edit</a>
                                            <form method="POST" action="{{ route('contacts.destroy', $contact) }}" data-confirm="Delete {{ $contact->name }}?">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-lg border border-orange-300 bg-orange-50 px-3 py-2 text-sm font-black text-orange-900" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
@endsection
