<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => User::normalizeEmail((string) $request->input('email')),
        ]);

        $attributes = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        Password::sendResetLink(['email' => $attributes['email']]);

        return back()->with('status', trans(Password::RESET_LINK_SENT));
    }
}
