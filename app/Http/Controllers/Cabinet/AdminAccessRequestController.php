<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminAccessRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminAccessRequestController extends Controller
{
    public function __invoke(Request $request, ActivityLogger $activity): RedirectResponse
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

        $activity->record(
            subject: $user,
            actor: $user,
            category: 'security',
            event: 'admin_access.requested',
            title: 'Admin access requested',
            description: 'A request for admin privileges was sent for review.',
            visibility: ActivityLog::VISIBILITY_BOTH,
        );

        return redirect()
            ->route('cabinet.security')
            ->with('status', 'Admin access request sent for review.');
    }
}
