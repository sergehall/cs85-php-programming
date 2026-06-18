@extends('layouts.app', ['title' => 'Create Account - CS85'])

@section('content')
    <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-start">
        <div class="grid content-start gap-4 py-3">
            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Create Cabinet Account</p>
            <h1 class="max-w-4xl text-4xl font-bold leading-none text-slate-950 md:text-6xl">Create one account for your CS85 workspace.</h1>
            <p class="max-w-3xl text-lg leading-8 text-slate-600">
                Registration creates a standard user role. Admin access stays separate and can be assigned intentionally later.
            </p>
        </div>

        <article class="rounded-lg border border-stone-300 bg-white p-6">
            <div class="grid gap-2">
                <h2 class="text-2xl font-bold text-slate-950">Create one</h2>
                <p class="text-sm leading-6 text-slate-600">Already registered? <a class="font-bold text-teal-800" href="{{ route('login') }}">Sign in</a>.</p>
            </div>

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-orange-200 bg-orange-50 p-4 text-sm font-bold text-orange-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <a
                class="mt-5 flex w-full items-center justify-center rounded-lg border border-slate-900 bg-slate-950 px-4 py-3 text-sm font-bold text-white no-underline transition hover:bg-teal-800"
                href="{{ route('auth.github.redirect') }}"
            >
                Continue with GitHub
            </a>

            <div class="my-6 flex items-center gap-3 text-xs font-bold uppercase tracking-normal text-slate-400">
                <span class="h-px flex-1 bg-stone-300"></span>
                <span>Email account</span>
                <span class="h-px flex-1 bg-stone-300"></span>
            </div>

            <form class="grid gap-4" method="POST" action="{{ route('register.store') }}">
                @csrf

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Name
                    <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Email
                    <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Password
                    <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="password" type="password" autocomplete="new-password" required>
                </label>

                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    Confirm password
                    <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal text-slate-950 outline-none transition focus:border-teal-700" name="password_confirmation" type="password" autocomplete="new-password" required>
                </label>

                <button class="rounded-lg bg-teal-800 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-950" type="submit">Create account</button>
            </form>
        </article>
    </section>
@endsection
