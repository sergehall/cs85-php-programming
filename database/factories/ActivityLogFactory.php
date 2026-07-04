<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_user_id' => User::factory(),
            'actor_user_id' => User::factory(),
            'category' => 'security',
            'event' => 'security.checked',
            'visibility' => ActivityLog::VISIBILITY_USER,
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'metadata' => [],
        ];
    }
}
