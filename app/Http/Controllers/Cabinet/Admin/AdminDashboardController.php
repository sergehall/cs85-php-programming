<?php

namespace App\Http\Controllers\Cabinet\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminAccessRequest;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalUsers = User::query()->count();
        $adminUsers = User::query()->where('role', 'admin')->count();
        $standardUsers = User::query()->where('role', 'user')->count();
        $pendingRequests = AdminAccessRequest::query()
            ->where('status', AdminAccessRequest::STATUS_PENDING)
            ->count();
        $githubConnectedUsers = User::query()->whereNotNull('github_id')->count();
        $mfaEnabledUsers = User::query()->whereNotNull('mfa_confirmed_at')->count();
        $blockedLoginUsers = User::query()->where('login_enabled', false)->count();

        return view('cabinet.admin-dashboard', [
            'summary' => [
                [
                    'label' => 'Total users',
                    'value' => (string) $totalUsers,
                    'detail' => 'Registered Laravel cabinet accounts.',
                ],
                [
                    'label' => 'Admin users',
                    'value' => (string) $adminUsers,
                    'detail' => "{$standardUsers} standard users remain outside admin tools.",
                ],
                [
                    'label' => 'Pending access',
                    'value' => (string) $pendingRequests,
                    'detail' => 'Admin access requests waiting for review.',
                ],
                [
                    'label' => 'Security adoption',
                    'value' => "{$mfaEnabledUsers} MFA",
                    'detail' => "{$githubConnectedUsers} GitHub-connected users, {$blockedLoginUsers} blocked logins.",
                ],
            ],
            'signals' => [
                [
                    'title' => 'GitHub identity',
                    'value' => "{$githubConnectedUsers} of {$totalUsers}",
                    'description' => 'Users with an external GitHub account connected.',
                ],
                [
                    'title' => 'Application MFA',
                    'value' => "{$mfaEnabledUsers} of {$totalUsers}",
                    'description' => 'Users protected by authenticator app codes.',
                ],
                [
                    'title' => 'Login access',
                    'value' => "{$blockedLoginUsers} blocked",
                    'description' => 'Standard users currently denied sign-in access.',
                ],
                [
                    'title' => 'Access review',
                    'value' => "{$pendingRequests} pending",
                    'description' => 'Requests that can be approved from User Management.',
                ],
            ],
            'actions' => [
                [
                    'title' => 'Review users',
                    'route' => 'cabinet.admin.users',
                    'status' => 'Live',
                    'description' => 'Review users, approve admin access requests, and revoke admin roles.',
                ],
                [
                    'title' => 'Review activity',
                    'route' => 'cabinet.activity',
                    'status' => 'Live',
                    'description' => 'Review user and administrative events recorded by the activity timeline.',
                ],
            ],
            'recentAdminActivity' => ActivityLog::query()
                ->with(['actorUser', 'subjectUser'])
                ->whereIn('visibility', [ActivityLog::VISIBILITY_ADMIN, ActivityLog::VISIBILITY_BOTH])
                ->where('category', 'admin')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
