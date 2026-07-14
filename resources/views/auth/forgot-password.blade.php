@extends('layouts.app', ['title' => 'Forgot Password - CS85'])

@section('content')
    <section class="mx-auto grid max-w-xl gap-5 rounded-lg border border-stone-300 bg-white p-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-950">Reset your password.</h1>
            <p class="mt-2 leading-7 text-slate-600">Enter your account email. If it is eligible, a reset link will be sent.</p>
        </div>
        @if (session('status'))
            <p class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-sm font-bold text-emerald-800" role="status">{{ session('status') }}</p>
        @endif
        <form class="grid gap-4" method="POST" action="{{ route('password.email') }}">
            @csrf
            <label class="grid gap-2 text-sm font-bold text-slate-700">Email
                <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            </label>
            @error('email')<p class="text-sm font-bold text-orange-700">{{ $message }}</p>@enderror
            <button class="rounded-lg bg-teal-800 px-4 py-3 text-sm font-bold text-white" type="submit">Send reset link</button>
        </form>
    </section>
@endsection
