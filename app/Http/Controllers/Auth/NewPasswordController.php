<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthSessionService;
use App\Services\SecurityAuditLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email'),
        ]);
    }

    public function store(
        Request $request,
        AuthSessionService $sessions,
        SecurityAuditLogger $audit,
    ): RedirectResponse {
        $request->merge([
            'email' => User::normalizeEmail((string) $request->input('email')),
        ]);

        $attributes = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $attributes,
            function (User $user, string $password) use ($request, $sessions, $audit): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'password_login_enabled' => true,
                    'remember_token' => Str::random(60),
                ])->save();

                $sessions->revokeAllSessions($user);
                event(new PasswordReset($user));

                $audit->record(
                    request: $request,
                    event: 'auth.password_reset',
                    outcome: 'success',
                    title: 'Password reset completed',
                    subject: $user,
                    actor: $user,
                    description: 'The account password was reset and existing sessions were revoked.',
                );
            },
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', trans($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => trans($status)]);
    }
}
