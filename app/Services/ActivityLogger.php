<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(
        ?User $subject,
        ?User $actor,
        string $category,
        string $event,
        string $title,
        ?string $description = null,
        string $visibility = ActivityLog::VISIBILITY_USER,
        array $metadata = [],
    ): ActivityLog {
        return ActivityLog::query()->create([
            'subject_user_id' => $subject?->getKey(),
            'actor_user_id' => $actor?->getKey(),
            'category' => $category,
            'event' => $event,
            'visibility' => $visibility,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function recordDaily(
        User $subject,
        User $actor,
        string $category,
        string $event,
        string $title,
        ?string $description = null,
        string $visibility = ActivityLog::VISIBILITY_USER,
        array $metadata = [],
    ): ActivityLog {
        $existingLog = ActivityLog::query()
            ->where('subject_user_id', $subject->getKey())
            ->where('actor_user_id', $actor->getKey())
            ->where('event', $event)
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if ($existingLog) {
            $existingLog->touch();

            return $existingLog;
        }

        return $this->record($subject, $actor, $category, $event, $title, $description, $visibility, $metadata);
    }
}
