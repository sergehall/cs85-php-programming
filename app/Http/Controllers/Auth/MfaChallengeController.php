<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\TotpAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MfaChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('auth.mfa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.mfa-challenge');
    }

    public function store(Request $request, TotpAuthenticator $totp, ActivityLogger $activity): RedirectResponse
    {
        $attributes = $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        $userId = $request->session()->get('auth.mfa.user_id');
        $user = is_numeric($userId) ? User::query()->find((int) $userId) : null;

        if (! $user instanceof User || ! $user->hasMfaEnabled() || ! $user->canLogIn()) {
            $request->session()->forget('auth.mfa');

            return redirect()->route('login');
        }

        if (! $this->verifyMfaToken($user, $attributes['code'], $totp)) {
            return redirect()
                ->route('mfa.challenge')
                ->withErrors(['code' => 'The MFA code or recovery code was not valid.']);
        }

        $remember = (bool) $request->session()->pull('auth.mfa.remember', false);
        $request->session()->forget('auth.mfa');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $activity->record(
            subject: $user,
            actor: $user,
            category: 'security',
            event: 'security.mfa_challenge_passed',
            title: 'MFA challenge completed',
            description: 'A sign-in attempt completed the application MFA challenge.',
            visibility: ActivityLog::VISIBILITY_USER,
        );

        return redirect()->intended(route('cabinet.dashboard'));
    }

    private function verifyMfaToken(User $user, string $token, TotpAuthenticator $totp): bool
    {
        if ($user->mfa_secret && $totp->verify($user->mfa_secret, $token)) {
            return true;
        }

        $recoveryCodeHashes = $user->getAttribute('mfa_recovery_codes');

        if (! is_array($recoveryCodeHashes)) {
            return false;
        }

        foreach ($recoveryCodeHashes as $index => $hash) {
            if (is_string($hash) && Hash::check(Str::upper(trim($token)), $hash)) {
                unset($recoveryCodeHashes[$index]);
                $user->forceFill(['mfa_recovery_codes' => array_values($recoveryCodeHashes)])->save();

                return true;
            }
        }

        return false;
    }
}
