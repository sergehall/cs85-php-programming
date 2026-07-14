<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Services\AI\Enums\AiMode;
use RuntimeException;

final class PromptBuilder
{
    public function __construct(
        private readonly ModelRouter $router,
    ) {}

    public function systemPrompt(AiMode $mode): string
    {
        $path = $this->router->configuration($mode)['prompt'];
        $prompt = is_file($path) ? file_get_contents($path) : false;

        if (! is_string($prompt) || trim($prompt) === '') {
            throw new RuntimeException("AI prompt for mode [{$mode->value}] is unavailable.");
        }

        return trim($prompt);
    }
}
