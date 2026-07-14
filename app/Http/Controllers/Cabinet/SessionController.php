<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    public function destroy(Request $request, string $sessionId, SecurityAuditLogger $audit): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User || $sessionId === $request->session()->getId()) {
            abort(403);
        }

        $deleted = DB::table((string) config('session.table', 'sessions'))
            ->where('id', $sessionId)
            ->where('user_id', $user->getKey())
            ->delete();

        abort_unless($deleted === 1, 404);

        $audit->record(
            request: $request,
            event: 'auth.session_revoked',
            outcome: 'success',
            title: 'Session revoked',
            subject: $user,
            actor: $user,
            description: 'A signed-in device session was revoked.',
        );

        return back()->with('status', 'Session revoked.');
    }

    public function destroyOthers(Request $request, SecurityAuditLogger $audit): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(403);
        }

        $deleted = DB::table((string) config('session.table', 'sessions'))
            ->where('user_id', $user->getKey())
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        $audit->record(
            request: $request,
            event: 'auth.other_sessions_revoked',
            outcome: 'success',
            title: 'Other sessions revoked',
            subject: $user,
            actor: $user,
            description: 'All other signed-in device sessions were revoked.',
            metadata: ['revoked_sessions' => $deleted],
        );

        return back()->with('status', 'Other sessions revoked.');
    }
}
