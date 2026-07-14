<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityAuditLogger;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request, SecurityAuditLogger $audit): RedirectResponse
    {
        $user = $request->user();

        if ($user instanceof User && ! $user->hasVerifiedEmail()) {
            $request->fulfill();

            $audit->record(
                request: $request,
                event: 'auth.email_verified',
                outcome: 'success',
                title: 'Email address verified',
                subject: $user,
                actor: $user,
                description: 'The account email address was verified.',
            );
        }

        return redirect()->intended(route('cabinet.dashboard'))->with('status', 'Email verified successfully.');
    }
}
