<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthSessionService;
use App\Services\SecurityAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function update(
        Request $request,
        AuthSessionService $sessions,
        SecurityAuditLogger $audit,
    ): RedirectResponse {
        $attributes = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $user->forceFill([
            'password' => Hash::make($attributes['password']),
            'password_login_enabled' => true,
            'remember_token' => Str::random(60),
        ])->save();

        $revoked = $sessions->revokeOtherSessions($request, $user);

        $audit->record(
            request: $request,
            event: 'auth.password_changed',
            outcome: 'success',
            title: 'Password changed',
            subject: $user,
            actor: $user,
            description: 'The account password was changed and other sessions were revoked.',
            metadata: ['revoked_sessions' => $revoked],
        );

        $request->session()->forget('auth.security_confirmation');

        return redirect()->route('cabinet.security')->with('status', 'Password updated and other sessions revoked.');
    }
}
