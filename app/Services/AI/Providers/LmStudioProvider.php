<?php

declare(strict_types=1);

namespace App\Services\AI\Providers;

final class LmStudioProvider extends OpenAiCompatibleProvider
{
    public function name(): string
    {
        return 'lm_studio';
    }

    public function displayName(): string
    {
        return 'LM Studio';
    }

    protected function requiresApiKey(): bool
    {
        return false;
    }
}
