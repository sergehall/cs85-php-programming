<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $userActivities = ActivityLog::query()
            ->with(['actorUser', 'subjectUser'])
            ->where('subject_user_id', $user->getKey())
            ->latest()
            ->limit(40)
            ->get();

        $adminActivities = $user->isAdmin()
            ? ActivityLog::query()
                ->with(['actorUser', 'subjectUser'])
                ->whereIn('visibility', [ActivityLog::VISIBILITY_ADMIN, ActivityLog::VISIBILITY_BOTH])
                ->latest()
                ->limit(40)
                ->get()
            : collect();

        return view('cabinet.activity', [
            'section' => config('cabinet.sections.activity'),
            'userActivities' => $userActivities,
            'adminActivities' => $adminActivities,
            'activityCounts' => [
                'user' => $userActivities->count(),
                'profile' => $userActivities->where('category', 'profile')->count(),
                'coursework' => $userActivities->where('category', 'coursework')->count(),
                'security' => $userActivities->where('category', 'security')->count(),
                'admin' => $adminActivities->count(),
            ],
        ]);
    }
}
