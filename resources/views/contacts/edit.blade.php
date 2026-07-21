@extends('layouts.app', [
    'title' => 'Edit Contact - CS85',
    'description' => 'Edit a contact with validated name, email, and phone fields.',
])

@section('content')
    <section class="mx-auto grid max-w-2xl gap-6 rounded-3xl border border-violet-200 bg-violet-50 p-6 shadow-xl shadow-violet-950/5 sm:p-8">
        <div>
            <p class="text-xs font-black uppercase tracking-[.14em] text-violet-800">Update</p>
            <h1 class="mt-2 text-4xl font-black tracking-tight text-slate-950">Edit contact</h1>
            <p class="mt-3 leading-7 text-slate-700">Update the contact name, email, or phone number.</p>
        </div>

        <form class="grid gap-5" method="POST" action="{{ route('contacts.update', $contact) }}">
            @csrf
            @method('PUT')
            @include('contacts._form', ['contact' => $contact])

            <div class="flex flex-wrap gap-3">
                <button class="rounded-xl bg-violet-700 px-5 py-3 font-black text-white transition hover:bg-violet-800" type="submit">Update contact</button>
                <a class="rounded-xl border border-stone-300 bg-white px-5 py-3 font-black text-slate-700 no-underline" href="{{ route('contacts.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
