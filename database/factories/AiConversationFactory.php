<?php

namespace Database\Factories;

use App\Models\AiConversation;
use App\Models\User;
use App\Services\AI\Enums\AiMode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiConversation>
 */
class AiConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'mode' => AiMode::General,
            'model' => config('ai.modes.general.model'),
        ];
    }
}
