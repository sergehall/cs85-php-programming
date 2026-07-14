<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function withSecurityConfirmation(User $user, string $method = 'password'): static
    {
        return $this->withSession([
            'auth.security_confirmation' => [
                'user_id' => $user->getKey(),
                'confirmed_at' => now()->getTimestamp(),
                'method' => $method,
            ],
        ]);
    }
}
