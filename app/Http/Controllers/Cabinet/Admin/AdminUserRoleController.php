<?php

namespace App\Http\Controllers\Cabinet\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAccessRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserRoleController extends Controller
{
    public function approve(Request $request, AdminAccessRequest $adminAccessRequest): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin instanceof User) {
            abort(403);
        }

        DB::transaction(function () use ($admin, $adminAccessRequest): void {
            $targetUser = $adminAccessRequest->user()->lockForUpdate()->firstOrFail();

            $targetUser->forceFill(['role' => 'admin'])->save();

            $adminAccessRequest->forceFill([
                'status' => AdminAccessRequest::STATUS_APPROVED,
                'reviewed_by' => $admin->getKey(),
                'reviewed_at' => now(),
            ])->save();
        });

        return redirect()
            ->route('cabinet.admin.users')
            ->with('status', 'Admin access granted.');
    }

    public function revoke(Request $request, User $user): RedirectResponse
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

        DB::transaction(function () use ($admin, $user): void {
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
        });

        return redirect()
            ->route('cabinet.admin.users')
            ->with('status', 'Admin access revoked.');
    }
}
