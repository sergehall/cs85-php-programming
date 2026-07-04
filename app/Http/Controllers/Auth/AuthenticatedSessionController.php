<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();

        if ($user instanceof User && ! $user->canLogIn()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'This account is not allowed to sign in right now. Contact an administrator.',
            ]);
        }

        if ($user instanceof User && $user->hasMfaEnabled()) {
            Auth::logout();
            $request->session()->put('auth.mfa.user_id', $user->getKey());
            $request->session()->put('auth.mfa.remember', $request->boolean('remember'));

            return redirect()->route('mfa.challenge');
        }

        return redirect()->intended(route('cabinet.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
