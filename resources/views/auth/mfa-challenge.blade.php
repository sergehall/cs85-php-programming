@extends('layouts.app', ['title' => 'MFA Challenge - CS85'])

@section('content')
    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-start">
        <div class="grid content-start gap-4 py-3">
            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Application MFA</p>
            <h1 class="max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">Verify your sign-in.</h1>
            <p class="max-w-3xl text-lg leading-8 text-slate-600">
                Enter a six-digit authenticator app code or one of your recovery codes to finish signing in.
            </p>
        </div>

        <article class="rounded-lg border border-stone-300 bg-white p-6">
            <div class="grid gap-2">
                <h2 class="text-2xl font-bold text-slate-950">MFA code</h2>
                <p class="text-sm leading-6 text-slate-600">This extra step protects your CS85 cabinet if your password is exposed.</p>
            </div>

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-orange-200 bg-orange-50 p-4 text-sm font-bold text-orange-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form class="mt-5 grid gap-4" method="POST" action="{{ route('mfa.challenge.store') }}">
                @csrf

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Authenticator or recovery code
                    <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required autofocus>
                </label>

                <button class="rounded-lg bg-teal-800 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-950" type="submit">Verify and continue</button>
            </form>
        </article>
    </section>
@endsection
