<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MfaTokenVerifier
{
    public function __construct(private readonly TotpAuthenticator $totp) {}

    public function verifyAndConsume(User $user, string $token): bool
    {
        return DB::transaction(function () use ($user, $token): bool {
            $lockedUser = User::query()->lockForUpdate()->find($user->getKey());

            if (! $lockedUser instanceof User || ! $lockedUser->hasMfaEnabled()) {
                return false;
            }

            if (is_string($lockedUser->mfa_secret)) {
                $timeSlice = $this->totp->matchingTimeSlice($lockedUser->mfa_secret, $token);
                $lastUsed = $lockedUser->getAttribute('mfa_last_used_time_slice');

                if ($timeSlice !== null && (! is_numeric($lastUsed) || $timeSlice > (int) $lastUsed)) {
                    $lockedUser->forceFill(['mfa_last_used_time_slice' => $timeSlice])->save();

                    return true;
                }
            }

            $recoveryCodeHashes = $lockedUser->getAttribute('mfa_recovery_codes');

            if (! is_array($recoveryCodeHashes)) {
                return false;
            }

            foreach ($recoveryCodeHashes as $index => $hash) {
                if (is_string($hash) && Hash::check(Str::upper(trim($token)), $hash)) {
                    unset($recoveryCodeHashes[$index]);
                    $lockedUser->forceFill([
                        'mfa_recovery_codes' => array_values($recoveryCodeHashes),
                    ])->save();

                    return true;
                }
            }

            return false;
        });
    }
}
