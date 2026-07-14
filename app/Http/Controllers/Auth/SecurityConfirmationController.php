<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MfaTokenVerifier;
use App\Services\SecurityAuditLogger;
use App\Services\SecurityConfirmationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SecurityConfirmationController extends Controller
{
    public function create(Request $request, SecurityConfirmationService $confirmation): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        if ($confirmation->isRecent($request, $user)) {
            return redirect()->intended(route('cabinet.security'));
        }

        return view('auth.confirm-security', ['user' => $user]);
    }

    public function store(
        Request $request,
        MfaTokenVerifier $mfa,
        SecurityConfirmationService $confirmation,
        SecurityAuditLogger $audit,
    ): RedirectResponse {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $attributes = $request->validate([
            'proof' => ['required', 'string', 'max:255'],
        ]);

        $method = $user->hasMfaEnabled() ? 'mfa' : 'password';
        $valid = $user->hasMfaEnabled()
            ? $mfa->verifyAndConsume($user, $attributes['proof'])
            : ($user->password_login_enabled && Hash::check($attributes['proof'], $user->password));

        if (! $valid) {
            $audit->record(
                request: $request,
                event: 'security.step_up_failed',
                outcome: 'failure',
                title: 'Security confirmation failed',
                subject: $user,
                actor: $user,
                description: 'A recent-authentication check did not complete.',
                metadata: ['method' => $method],
            );

            throw ValidationException::withMessages([
                'proof' => 'The security confirmation was not valid.',
            ]);
        }

        $confirmation->confirm($request, $user, $method);

        $audit->record(
            request: $request,
            event: 'security.step_up_succeeded',
            outcome: 'success',
            title: 'Security confirmation completed',
            subject: $user,
            actor: $user,
            description: 'Recent authentication was confirmed for sensitive account actions.',
            metadata: ['method' => $method],
        );

        return redirect()->intended(route('cabinet.security'));
    }
}
