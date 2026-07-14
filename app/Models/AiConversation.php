<?php

namespace App\Models;

use App\Services\AI\Enums\AiMode;
use Database\Factories\AiConversationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $user_id
 * @property string $public_uuid
 * @property string $title
 * @property AiMode $mode
 * @property string $model
 */
#[Fillable(['user_id', 'title', 'mode', 'model'])]
class AiConversation extends Model
{
    /** @use HasFactory<AiConversationFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (AiConversation $conversation): void {
            $conversation->public_uuid ??= (string) Str::uuid();
        });
    }

    protected function casts(): array
    {
        return [
            'mode' => AiMode::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<AiMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class)->oldest();
    }

    /**
     * @return HasMany<AiRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(AiRequest::class);
    }
}
