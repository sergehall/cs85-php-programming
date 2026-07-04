<?php

namespace App\Http\Controllers\Cabinet\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAccessRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUsersController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);
        $users = User::query()
            ->with('adminAccessRequest')
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $search = '%'.$filters['search'].'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('github_username', 'like', $search);
                });
            })
            ->when($filters['role'] !== 'all', function (Builder $query) use ($filters): void {
                $query->where('role', $filters['role']);
            });

        match ($filters['security']) {
            'mfa_enabled' => $users->whereNotNull('mfa_confirmed_at'),
            'mfa_missing' => $users->whereNull('mfa_confirmed_at'),
            'github_connected' => $users->whereNotNull('github_id'),
            'github_missing' => $users->whereNull('github_id'),
            'login_allowed' => $users->where('login_enabled', true),
            'login_blocked' => $users->where('login_enabled', false),
            default => null,
        };

        match ($filters['request']) {
            AdminAccessRequest::STATUS_PENDING,
            AdminAccessRequest::STATUS_APPROVED,
            AdminAccessRequest::STATUS_REVOKED => $users->whereHas('adminAccessRequest', function (Builder $query) use ($filters): void {
                $query->where('status', $filters['request']);
            }),
            'none' => $users->whereDoesntHave('adminAccessRequest'),
            default => null,
        };

        return view('cabinet.admin-users', [
            'section' => config('cabinet.admin.sections.users'),
            'filters' => $filters,
            'metrics' => [
                [
                    'label' => 'Total users',
                    'value' => (string) User::query()->count(),
                    'detail' => 'All registered cabinet accounts.',
                ],
                [
                    'label' => 'Admins',
                    'value' => (string) User::query()->where('role', 'admin')->count(),
                    'detail' => 'Users with protected admin access.',
                ],
                [
                    'label' => 'Pending requests',
                    'value' => (string) AdminAccessRequest::query()->where('status', AdminAccessRequest::STATUS_PENDING)->count(),
                    'detail' => 'Waiting for an admin review.',
                ],
                [
                    'label' => 'Blocked logins',
                    'value' => (string) User::query()->where('login_enabled', false)->count(),
                    'detail' => 'Standard users currently denied sign-in access.',
                ],
            ],
            'pendingRequests' => AdminAccessRequest::query()
                ->with('user')
                ->where('status', AdminAccessRequest::STATUS_PENDING)
                ->latest('requested_at')
                ->get(),
            'users' => $users
                ->orderBy('name')
                ->paginate(10)
                ->appends(array_filter($filters, fn (string $value): bool => $value !== '' && $value !== 'all')),
        ]);
    }

    /**
     * @return array{search: string, role: string, security: string, request: string}
     */
    private function filtersFromRequest(Request $request): array
    {
        $search = $request->query('search');
        $role = $request->query('role');
        $security = $request->query('security');
        $accessRequest = $request->query('request');

        return [
            'search' => is_string($search) ? trim($search) : '',
            'role' => is_string($role) && in_array($role, ['all', 'admin', 'user'], true) ? $role : 'all',
            'security' => is_string($security) && in_array($security, ['all', 'mfa_enabled', 'mfa_missing', 'github_connected', 'github_missing', 'login_allowed', 'login_blocked'], true) ? $security : 'all',
            'request' => is_string($accessRequest) && in_array($accessRequest, ['all', 'pending', 'approved', 'revoked', 'none'], true) ? $accessRequest : 'all',
        ];
    }
}
