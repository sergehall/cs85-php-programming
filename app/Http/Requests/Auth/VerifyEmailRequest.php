<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Str;

final class VerifyEmailRequest extends EmailVerificationRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $routeUuid = (string) $this->route('id');

        if (! $user instanceof User) {
            return false;
        }

        if (! Str::isUuid($routeUuid) || ! hash_equals($user->public_uuid, $routeUuid)) {
            return false;
        }

        return hash_equals(
            sha1($user->getEmailForVerification()),
            (string) $this->route('hash'),
        );
    }
}
