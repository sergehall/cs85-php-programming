<?php

declare(strict_types=1);

namespace App\Services\AI\Providers;

final class OpenAiProvider extends OpenAiCompatibleProvider
{
    public function name(): string
    {
        return 'openai';
    }

    public function displayName(): string
    {
        return 'OpenAI API';
    }

    protected function requiresApiKey(): bool
    {
        return true;
    }
}
