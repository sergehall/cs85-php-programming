<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;

class SecurityConfirmationService
{
    public function confirm(Request $request, User $user, string $method): void
    {
        $request->session()->put('auth.security_confirmation', [
            'user_id' => $user->getKey(),
            'confirmed_at' => now()->getTimestamp(),
            'method' => $method,
        ]);
    }

    public function isRecent(Request $request, User $user): bool
    {
        $confirmation = $request->session()->get('auth.security_confirmation');

        if (! is_array($confirmation)) {
            return false;
        }

        $confirmedAt = $confirmation['confirmed_at'] ?? null;

        return ($confirmation['user_id'] ?? null) === $user->getKey()
            && is_numeric($confirmedAt)
            && now()->getTimestamp() - (int) $confirmedAt <= (int) config('auth_security.step_up_ttl_seconds', 900);
    }
}
