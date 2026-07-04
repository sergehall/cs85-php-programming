<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\TotpAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MfaController extends Controller
{
    public function start(Request $request, TotpAuthenticator $totp): RedirectResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user->hasMfaEnabled()) {
            return redirect()
                ->route('cabinet.security')
                ->with('status', 'Application MFA is already enabled.');
        }

        $secret = $totp->generateSecret();

        $request->session()->put('mfa_setup.secret', $secret);
        $request->session()->put('mfa_setup.provisioning_uri', $totp->provisioningUri('CS85 PHP Programming', $user->email, $secret));

        return redirect()
            ->route('cabinet.security')
            ->with('status', 'Scan the MFA QR code or enter the secret manually, then confirm the six-digit code.');
    }

    public function confirm(Request $request, TotpAuthenticator $totp, ActivityLogger $activity): RedirectResponse
    {
        $user = $this->authenticatedUser($request);
        $secret = $request->session()->get('mfa_setup.secret');

        if (! is_string($secret)) {
            return redirect()
                ->route('cabinet.security')
                ->withErrors(['mfa' => 'Start MFA setup before confirming a code.']);
        }

        $attributes = $request->validate([
            'code' => ['required', 'string', 'max:16'],
        ]);

        if (! $totp->verify($secret, $attributes['code'])) {
            return redirect()
                ->route('cabinet.security')
                ->withErrors(['mfa' => 'The authenticator code was not valid.']);
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->forceFill([
            'mfa_secret' => $secret,
            'mfa_recovery_codes' => collect($recoveryCodes)->map(fn (string $code): string => Hash::make($code))->all(),
            'mfa_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('mfa_setup');
        $request->session()->flash('mfa_recovery_codes', $recoveryCodes);

        $activity->record(
            subject: $user,
            actor: $user,
            category: 'security',
            event: 'security.mfa_enabled',
            title: 'Application MFA enabled',
            description: 'Authenticator app MFA was enabled for this account.',
            visibility: ActivityLog::VISIBILITY_BOTH,
        );

        return redirect()
            ->route('cabinet.security')
            ->with('status', 'Application MFA enabled. Store your recovery codes now.');
    }

    public function destroy(Request $request, TotpAuthenticator $totp, ActivityLogger $activity): RedirectResponse
    {
        $user = $this->authenticatedUser($request);

        $attributes = $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        if (! $this->verifyMfaToken($user, $attributes['code'], $totp)) {
            return redirect()
                ->route('cabinet.security')
                ->withErrors(['mfa' => 'The MFA code or recovery code was not valid.']);
        }

        $user->forceFill([
            'mfa_secret' => null,
            'mfa_recovery_codes' => null,
            'mfa_confirmed_at' => null,
        ])->save();

        $activity->record(
            subject: $user,
            actor: $user,
            category: 'security',
            event: 'security.mfa_disabled',
            title: 'Application MFA disabled',
            description: 'Authenticator app MFA was disabled for this account.',
            visibility: ActivityLog::VISIBILITY_BOTH,
        );

        return redirect()
            ->route('cabinet.security')
            ->with('status', 'Application MFA disabled.');
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
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

    /**
     * @return list<string>
     */
    private function generateRecoveryCodes(): array
    {
        return collect(range(1, 8))
            ->map(fn (): string => Str::upper(Str::random(4).'-'.Str::random(4)))
            ->all();
    }
}
