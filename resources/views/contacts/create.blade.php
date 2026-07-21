@extends('layouts.app', [
    'title' => 'Add Contact - CS85',
    'description' => 'Create a contact with validated name, email, and phone fields.',
])

@section('content')
    <section class="mx-auto grid max-w-2xl gap-6 rounded-3xl border border-emerald-200 bg-emerald-50 p-6 shadow-xl shadow-emerald-950/5 sm:p-8">
        <div>
            <p class="text-xs font-black uppercase tracking-[.14em] text-emerald-800">Create</p>
            <h1 class="mt-2 text-4xl font-black tracking-tight text-slate-950">Add contact</h1>
            <p class="mt-3 leading-7 text-slate-700">All three fields are required.</p>
        </div>

        <form class="grid gap-5" method="POST" action="{{ route('contacts.store') }}">
            @csrf
            @include('contacts._form', ['contact' => null])

            <div class="flex flex-wrap gap-3">
                <button class="rounded-xl bg-emerald-700 px-5 py-3 font-black text-white transition hover:bg-emerald-800" type="submit">Save contact</button>
                <a class="rounded-xl border border-stone-300 bg-white px-5 py-3 font-black text-slate-700 no-underline" href="{{ route('contacts.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
