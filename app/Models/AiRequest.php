<?php

namespace App\Models;

use Database\Factories\AiRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $ai_conversation_id
 * @property int|null $user_message_id
 * @property int $user_id
 * @property string $provider
 * @property string $status
 * @property Carbon|null $created_at
 */
#[Fillable([
    'ai_conversation_id',
    'user_message_id',
    'user_id',
    'mode',
    'provider',
    'model',
    'prompt_tokens',
    'completion_tokens',
    'latency_ms',
    'status',
    'error_code',
])]
class AiRequest extends Model
{
    /** @use HasFactory<AiRequestFactory> */
    use HasFactory;

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    /**
     * @return BelongsTo<AiConversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'ai_conversation_id');
    }

    /**
     * @return BelongsTo<AiMessage, $this>
     */
    public function userMessage(): BelongsTo
    {
        return $this->belongsTo(AiMessage::class, 'user_message_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRetryable(): bool
    {
        if ($this->status === self::STATUS_FAILED) {
            return true;
        }

        if ($this->status !== self::STATUS_PROCESSING || $this->created_at === null) {
            return false;
        }

        $providerTimeout = (int) config("ai.providers.{$this->provider}.timeout", 180);
        $staleAfterSeconds = max(60, $providerTimeout + 30);

        return $this->created_at->lte(now()->subSeconds($staleAfterSeconds));
    }
}
