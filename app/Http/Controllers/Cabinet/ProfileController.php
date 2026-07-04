<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ActivityLogger;
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

    public function update(UpdateProfileRequest $request, ActivityLogger $activity): RedirectResponse
    {
        $user = $request->user();
        $attributes = $request->validated();
        $attributes['name'] = trim($attributes['first_name'].' '.$attributes['last_name']);

        $user->forceFill($attributes)->save();

        $activity->record(
            subject: $user,
            actor: $user,
            category: 'profile',
            event: 'profile.updated',
            title: 'Profile updated',
            description: 'Profile identity, portfolio links, bio, or technical skills were updated.',
        );

        return redirect()
            ->route('cabinet.profile')
            ->with('status', 'Profile updated successfully.');
    }
}
