<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, SecurityAuditLogger $audit): RedirectResponse
    {
        $request->merge([
            'email' => User::normalizeEmail((string) $request->input('email')),
        ]);

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::query()->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
            'password_login_enabled' => true,
            'role' => 'user',
        ]);

        $audit->record(
            request: $request,
            event: 'auth.registered',
            outcome: 'success',
            title: 'Account registered',
            subject: $user,
            actor: $user,
            description: 'A standard CS85 user account was created.',
            metadata: ['provider' => 'password'],
        );

        Auth::login($user);
        $request->session()->regenerate();
        $user->sendEmailVerificationNotification();

        return redirect()->intended(route('cabinet.dashboard'));
    }
}
