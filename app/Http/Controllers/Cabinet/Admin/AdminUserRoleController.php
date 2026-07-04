<?php

namespace App\Http\Controllers\Cabinet\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminAccessRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserRoleController extends Controller
{
    public function approve(Request $request, AdminAccessRequest $adminAccessRequest, ActivityLogger $activity): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin instanceof User) {
            abort(403);
        }

        DB::transaction(function () use ($admin, $adminAccessRequest, $activity): void {
            $targetUser = $adminAccessRequest->user()->lockForUpdate()->firstOrFail();

            $targetUser->forceFill(['role' => 'admin'])->save();

            $adminAccessRequest->forceFill([
                'status' => AdminAccessRequest::STATUS_APPROVED,
                'reviewed_by' => $admin->getKey(),
                'reviewed_at' => now(),
            ])->save();

            $activity->record(
                subject: $targetUser,
                actor: $admin,
                category: 'admin',
                event: 'admin_access.granted',
                title: 'Admin access granted',
                description: 'An administrator approved the admin access request.',
                visibility: ActivityLog::VISIBILITY_BOTH,
            );
        });

        return redirect()
            ->route('cabinet.admin.users')
            ->with('status', 'Admin access granted.');
    }

    public function revoke(Request $request, User $user, ActivityLogger $activity): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin instanceof User) {
            abort(403);
        }

        if ($admin->is($user)) {
            return redirect()
                ->route('cabinet.admin.users')
                ->withErrors(['role' => 'You cannot revoke your own admin access.']);
        }

        DB::transaction(function () use ($admin, $user, $activity): void {
            $user->forceFill(['role' => 'user'])->save();

            AdminAccessRequest::query()->updateOrCreate(
                ['user_id' => $user->getKey()],
                [
                    'status' => AdminAccessRequest::STATUS_REVOKED,
                    'requested_at' => now(),
                    'reviewed_by' => $admin->getKey(),
                    'reviewed_at' => now(),
                ],
            );

            $activity->record(
                subject: $user,
                actor: $admin,
                category: 'admin',
                event: 'admin_access.revoked',
                title: 'Admin access revoked',
                description: 'An administrator changed this account back to a standard user role.',
                visibility: ActivityLog::VISIBILITY_BOTH,
            );
        });

        return redirect()
            ->route('cabinet.admin.users')
            ->with('status', 'Admin access revoked.');
    }
}
