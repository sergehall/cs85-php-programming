<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthSessionService
{
    public function revokeOtherSessions(Request $request, User $user): int
    {
        $deleted = 0;

        if (config('session.driver') === 'database') {
            $deleted = DB::table((string) config('session.table', 'sessions'))
                ->where('user_id', $user->getKey())
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        $user->setRememberToken(Str::random(60));
        $user->save();

        return $deleted;
    }

    public function revokeAllSessions(User $user): int
    {
        $deleted = 0;

        if (config('session.driver') === 'database') {
            $deleted = DB::table((string) config('session.table', 'sessions'))
                ->where('user_id', $user->getKey())
                ->delete();
        }

        $user->setRememberToken(Str::random(60));
        $user->save();

        return $deleted;
    }
}
