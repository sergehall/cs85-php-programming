<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    private const USER_ACTIVITY_PER_PAGE = 5;

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $userActivityQuery = ActivityLog::query()
            ->where('subject_user_id', $user->getKey())
            ->whereIn('visibility', [ActivityLog::VISIBILITY_USER, ActivityLog::VISIBILITY_BOTH]);

        $userActivities = (clone $userActivityQuery)
            ->with(['actorUser', 'subjectUser'])
            ->latest()
            ->paginate(self::USER_ACTIVITY_PER_PAGE, ['*'], 'my_activity_page')
            ->withQueryString();

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
                'user' => (clone $userActivityQuery)->count(),
                'profile' => (clone $userActivityQuery)->where('category', 'profile')->count(),
                'coursework' => (clone $userActivityQuery)->where('category', 'coursework')->count(),
                'security' => (clone $userActivityQuery)->where('category', 'security')->count(),
                'admin' => $adminActivities->count(),
            ],
        ]);
    }
}
