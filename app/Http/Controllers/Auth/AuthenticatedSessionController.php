<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityAuditLogger;
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

    public function store(Request $request, SecurityAuditLogger $audit): RedirectResponse
    {
        $request->merge([
            'email' => User::normalizeEmail((string) $request->input('email')),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials + ['password_login_enabled' => true], $request->boolean('remember'))) {
            $subject = User::query()->where('email', $credentials['email'])->first();

            $audit->record(
                request: $request,
                event: 'auth.login_failed',
                outcome: 'failure',
                title: 'Password sign-in failed',
                subject: $subject,
                description: 'A password sign-in attempt did not complete.',
                metadata: ['identity_hash' => $audit->identityHash($credentials['email']), 'provider' => 'password'],
            );

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();

        if ($user instanceof User && ! $user->canLogIn()) {
            $audit->record(
                request: $request,
                event: 'auth.login_blocked',
                outcome: 'blocked',
                title: 'Blocked account sign-in prevented',
                subject: $user,
                description: 'A valid password was supplied for an account with disabled sign-in access.',
                metadata: ['provider' => 'password'],
            );

            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'This account is not allowed to sign in right now. Contact an administrator.',
            ]);
        }

        if ($user instanceof User && $user->hasMfaEnabled()) {
            Auth::logout();
            $request->session()->put('auth.mfa.user_id', $user->getKey());
            $request->session()->put('auth.mfa.remember', $request->boolean('remember'));
            $request->session()->put('auth.mfa.started_at', now()->getTimestamp());

            $audit->record(
                request: $request,
                event: 'auth.first_factor_passed',
                outcome: 'pending_mfa',
                title: 'Password accepted; MFA required',
                subject: $user,
                description: 'The password factor passed and the sign-in is waiting for MFA.',
                metadata: ['provider' => 'password'],
            );

            return redirect()->route('mfa.challenge');
        }

        if ($user instanceof User) {
            $audit->record(
                request: $request,
                event: 'auth.login_succeeded',
                outcome: 'success',
                title: 'Password sign-in completed',
                subject: $user,
                actor: $user,
                description: 'A password sign-in completed successfully.',
                metadata: ['provider' => 'password', 'remembered' => $request->boolean('remember')],
            );
        }

        return redirect()->intended(route('cabinet.dashboard'));
    }

    public function destroy(Request $request, SecurityAuditLogger $audit): RedirectResponse
    {
        $user = $request->user();

        if ($user instanceof User) {
            $audit->record(
                request: $request,
                event: 'auth.logout',
                outcome: 'success',
                title: 'Session signed out',
                subject: $user,
                actor: $user,
                description: 'The current application session was signed out.',
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
