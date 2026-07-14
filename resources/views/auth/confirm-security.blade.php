@extends('layouts.app', ['title' => 'Confirm Security - CS85'])

@section('content')
    <section class="mx-auto grid max-w-xl gap-5 rounded-lg border border-stone-300 bg-white p-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-normal text-orange-700">Step-up authentication</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950">Confirm this sensitive action.</h1>
            <p class="mt-3 leading-7 text-slate-600">
                @if ($user->hasMfaEnabled())
                    Enter an authenticator or recovery code.
                @elseif ($user->password_login_enabled)
                    Enter your current password.
                @else
                    Confirm ownership again through the connected GitHub account.
                @endif
            </p>
        </div>

        @if ($user->hasMfaEnabled() || $user->password_login_enabled)
            <form class="grid gap-4" method="POST" action="{{ route('security.confirm.store') }}">
                @csrf
                <label class="grid gap-2 text-sm font-bold text-slate-700">
                    {{ $user->hasMfaEnabled() ? 'Authenticator or recovery code' : 'Current password' }}
                    <input class="rounded-lg border border-stone-300 px-3 py-3 font-normal" name="proof" type="{{ $user->hasMfaEnabled() ? 'text' : 'password' }}" autocomplete="{{ $user->hasMfaEnabled() ? 'one-time-code' : 'current-password' }}" required autofocus>
                </label>
                @error('proof')<p class="text-sm font-bold text-orange-700">{{ $message }}</p>@enderror
                <button class="rounded-lg bg-teal-800 px-4 py-3 text-sm font-bold text-white" type="submit">Confirm identity</button>
            </form>
        @elseif ($user->github_id)
            <a class="rounded-lg bg-slate-950 px-4 py-3 text-center text-sm font-bold text-white no-underline" href="{{ route('auth.github.redirect', ['purpose' => 'step_up']) }}">Confirm with GitHub</a>
        @else
            <p class="rounded-lg border border-orange-300 bg-orange-50 p-4 text-sm font-bold text-orange-800">No eligible confirmation method is configured. Contact an administrator.</p>
        @endif
    </section>
@endsection
