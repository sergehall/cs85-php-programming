<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\MfaTokenVerifier;
use App\Services\SecurityAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MfaChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $this->hasValidPendingChallenge($request)) {
            $request->session()->forget('auth.mfa');

            return redirect()->route('login');
        }

        return view('auth.mfa-challenge');
    }

    public function store(Request $request, MfaTokenVerifier $mfa, SecurityAuditLogger $audit): RedirectResponse
    {
        $attributes = $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        if (! $this->hasValidPendingChallenge($request)) {
            $request->session()->forget('auth.mfa');

            return redirect()->route('login')->withErrors(['email' => 'The sign-in challenge expired. Please sign in again.']);
        }

        $userId = $request->session()->get('auth.mfa.user_id');
        $user = is_numeric($userId) ? User::query()->find((int) $userId) : null;

        if (! $user instanceof User || ! $user->hasMfaEnabled() || ! $user->canLogIn()) {
            $request->session()->forget('auth.mfa');

            return redirect()->route('login');
        }

        if (! $mfa->verifyAndConsume($user, $attributes['code'])) {
            $audit->record(
                request: $request,
                event: 'security.mfa_challenge_failed',
                outcome: 'failure',
                title: 'MFA challenge failed',
                subject: $user,
                description: 'A sign-in MFA challenge did not complete.',
            );

            return redirect()
                ->route('mfa.challenge')
                ->withErrors(['code' => 'The MFA code or recovery code was not valid.']);
        }

        $remember = (bool) $request->session()->pull('auth.mfa.remember', false);
        $request->session()->forget('auth.mfa');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $audit->record(
            request: $request,
            event: 'security.mfa_challenge_passed',
            outcome: 'success',
            title: 'MFA challenge completed',
            subject: $user,
            actor: $user,
            description: 'A sign-in attempt completed the application MFA challenge.',
            visibility: ActivityLog::VISIBILITY_USER,
            metadata: ['remembered' => $remember],
        );

        $audit->record(
            request: $request,
            event: 'auth.login_succeeded',
            outcome: 'success',
            title: 'MFA-protected sign-in completed',
            subject: $user,
            actor: $user,
            description: 'A sign-in completed after successful MFA.',
            metadata: ['provider' => 'application_mfa', 'remembered' => $remember],
        );

        return redirect()->intended(route('cabinet.dashboard'));
    }

    private function hasValidPendingChallenge(Request $request): bool
    {
        $startedAt = $request->session()->get('auth.mfa.started_at');

        return $request->session()->has('auth.mfa.user_id')
            && is_numeric($startedAt)
            && now()->getTimestamp() - (int) $startedAt <= (int) config('auth_security.mfa_challenge_ttl_seconds', 300);
    }
}
