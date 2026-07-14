<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request, SecurityAuditLogger $audit): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User || $user->hasVerifiedEmail()) {
            return redirect()->intended(route('cabinet.dashboard'));
        }

        $user->sendEmailVerificationNotification();

        $audit->record(
            request: $request,
            event: 'auth.email_verification_requested',
            outcome: 'success',
            title: 'Verification email requested',
            subject: $user,
            actor: $user,
            description: 'A new email verification link was requested.',
        );

        return back()->with('status', 'verification-link-sent');
    }
}
