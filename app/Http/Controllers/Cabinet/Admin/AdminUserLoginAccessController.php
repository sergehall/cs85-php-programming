<?php

namespace App\Http\Controllers\Cabinet\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserLoginAccessController extends Controller
{
    public function disable(Request $request, User $user, ActivityLogger $activity): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin instanceof User) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return redirect()
                ->route('cabinet.admin.users')
                ->withErrors(['login' => 'Admin login access cannot be disabled from this user management action.']);
        }

        DB::transaction(function () use ($admin, $user, $activity): void {
            $user->forceFill(['login_enabled' => false])->save();

            $activity->record(
                subject: $user,
                actor: $admin,
                category: 'admin',
                event: 'user_login.disabled',
                title: 'User login disabled',
                description: 'An administrator disabled sign-in access for this standard user.',
                visibility: ActivityLog::VISIBILITY_BOTH,
            );
        });

        return redirect()
            ->route('cabinet.admin.users')
            ->with('status', 'User login access disabled.');
    }

    public function enable(Request $request, User $user, ActivityLogger $activity): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin instanceof User) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return redirect()
                ->route('cabinet.admin.users')
                ->withErrors(['login' => 'Admin login access is always managed through protected admin role controls.']);
        }

        DB::transaction(function () use ($admin, $user, $activity): void {
            $user->forceFill(['login_enabled' => true])->save();

            $activity->record(
                subject: $user,
                actor: $admin,
                category: 'admin',
                event: 'user_login.enabled',
                title: 'User login enabled',
                description: 'An administrator restored sign-in access for this standard user.',
                visibility: ActivityLog::VISIBILITY_BOTH,
            );
        });

        return redirect()
            ->route('cabinet.admin.users')
            ->with('status', 'User login access enabled.');
    }
}
