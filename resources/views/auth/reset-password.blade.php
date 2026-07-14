@extends('layouts.app', ['title' => 'Reset Password - CS85'])

@section('content')
    <section class="mx-auto grid max-w-xl gap-5 rounded-lg border border-stone-300 bg-white p-6">
        <h1 class="text-3xl font-bold text-slate-950">Choose a new password.</h1>
        <form class="grid gap-4" method="POST" action="{{ route('password.store') }}">
            @csrf
            <input name="token" type="hidden" value="{{ $token }}">
            <label class="grid gap-2 text-sm font-bold text-slate-700">Email
                <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required>
            </label>
            <label class="grid gap-2 text-sm font-bold text-slate-700">New password
                <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal" name="password" type="password" autocomplete="new-password" required>
            </label>
            <label class="grid gap-2 text-sm font-bold text-slate-700">Confirm password
                <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal" name="password_confirmation" type="password" autocomplete="new-password" required>
            </label>
            @if ($errors->any())
                <div class="rounded-lg border border-orange-300 bg-orange-50 p-4 text-sm font-bold text-orange-800">{{ $errors->first() }}</div>
            @endif
            <button class="rounded-lg bg-teal-800 px-4 py-3 text-sm font-bold text-white" type="submit">Reset password</button>
        </form>
    </section>
@endsection
