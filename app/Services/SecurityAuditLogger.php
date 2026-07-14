<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SecurityAuditLogger
{
    public function __construct(private readonly ActivityLogger $activity) {}

    /**
     * @param  array<string, bool|float|int|string|null>  $metadata
     */
    public function record(
        Request $request,
        string $event,
        string $outcome,
        string $title,
        ?User $subject = null,
        ?User $actor = null,
        ?string $description = null,
        string $visibility = ActivityLog::VISIBILITY_BOTH,
        array $metadata = [],
    ): ActivityLog {
        $context = array_filter([
            'outcome' => $outcome,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            'session_id_hash' => $request->hasSession() && $request->session()->getId() !== ''
                ? hash('sha256', $request->session()->getId())
                : null,
            'route' => $request->route()?->getName(),
            ...$metadata,
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        $log = $this->activity->record(
            subject: $subject,
            actor: $actor,
            category: 'security',
            event: $event,
            title: $title,
            description: $description,
            visibility: $visibility,
            metadata: $context,
        );

        Log::channel('security')->info($event, [
            'audit_log_id' => $log->getKey(),
            'subject_user_id' => $subject?->getKey(),
            'actor_user_id' => $actor?->getKey(),
            ...$context,
        ]);

        return $log;
    }

    public function identityHash(string $email): string
    {
        return hash_hmac('sha256', User::normalizeEmail($email), (string) config('app.key'));
    }
}
