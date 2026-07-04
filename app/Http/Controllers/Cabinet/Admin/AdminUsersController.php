<?php

namespace App\Http\Controllers\Cabinet\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAccessRequest;
use App\Models\User;
use Illuminate\View\View;

class AdminUsersController extends Controller
{
    public function __invoke(): View
    {
        return view('cabinet.admin-users', [
            'section' => config('cabinet.admin.sections.users'),
            'pendingRequests' => AdminAccessRequest::query()
                ->with('user')
                ->where('status', AdminAccessRequest::STATUS_PENDING)
                ->latest('requested_at')
                ->get(),
            'users' => User::query()
                ->with('adminAccessRequest')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
