<?php

namespace Database\Factories;

use App\Models\AdminAccessRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminAccessRequest>
 */
class AdminAccessRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => AdminAccessRequest::STATUS_PENDING,
            'requested_at' => now(),
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }
}
