<?php

namespace Database\Factories;

use App\Models\AiConversation;
use App\Models\AiRequest;
use App\Models\User;
use App\Services\AI\Enums\AiMode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiRequest>
 */
class AiRequestFactory extends Factory
{
    public function definition(): array
    {
        $user = User::factory();
        $conversation = AiConversation::factory()->for($user);

        return [
            'ai_conversation_id' => $conversation,
            'user_id' => $user,
            'mode' => AiMode::General->value,
            'provider' => 'lm_studio',
            'model' => config('ai.modes.general.model'),
            'prompt_tokens' => 20,
            'completion_tokens' => 10,
            'latency_ms' => 250,
            'status' => AiRequest::STATUS_COMPLETED,
            'error_code' => null,
        ];
    }
}
