<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('cabinet.profile', [
            'user' => $request->user(),
            'section' => config('cabinet.sections.profile'),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $attributes = $request->validated();
        $attributes['name'] = trim($attributes['first_name'].' '.$attributes['last_name']);

        $user->forceFill($attributes)->save();

        return redirect()
            ->route('cabinet.profile')
            ->with('status', 'Profile updated successfully.');
    }
}
