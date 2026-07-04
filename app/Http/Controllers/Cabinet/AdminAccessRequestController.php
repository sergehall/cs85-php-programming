<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\AdminAccessRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminAccessRequestController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return redirect()
                ->route('cabinet.security')
                ->with('status', 'Your account already has admin access.');
        }

        $existingRequest = AdminAccessRequest::query()
            ->where('user_id', $user->getKey())
            ->first();

        if ($existingRequest?->isPending()) {
            return redirect()
                ->route('cabinet.security')
                ->with('status', 'Your admin access request is already pending review.');
        }

        AdminAccessRequest::query()->updateOrCreate(
            ['user_id' => $user->getKey()],
            [
                'status' => AdminAccessRequest::STATUS_PENDING,
                'requested_at' => now(),
                'reviewed_by' => null,
                'reviewed_at' => null,
            ],
        );

        return redirect()
            ->route('cabinet.security')
            ->with('status', 'Admin access request sent for review.');
    }
}
