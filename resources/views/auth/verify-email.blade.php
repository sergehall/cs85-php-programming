@extends('layouts.app', ['title' => 'Verify Email - CS85'])

@section('content')
    <section class="mx-auto grid max-w-2xl gap-5 rounded-lg border border-stone-300 bg-white p-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Account verification</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950">Verify your email address.</h1>
            <p class="mt-3 leading-7 text-slate-600">Open the verification link sent to <strong>{{ auth()->user()->email }}</strong> before entering the cabinet.</p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <p class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-sm font-bold text-emerald-800" role="status">A new verification link was sent.</p>
        @endif

        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="rounded-lg bg-teal-800 px-4 py-3 text-sm font-bold text-white" type="submit">Resend verification email</button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg border border-stone-300 px-4 py-3 text-sm font-bold text-slate-700" type="submit">Log out</button>
            </form>
        </div>
    </section>
@endsection
