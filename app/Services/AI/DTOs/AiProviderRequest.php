<?php

declare(strict_types=1);

namespace App\Services\AI\DTOs;

final readonly class AiProviderRequest
{
    /**
     * @param  list<array<string, mixed>>  $messages
     * @param  list<array<string, mixed>>  $tools
     */
    public function __construct(
        public string $model,
        public array $messages,
        public array $tools,
        public float $temperature,
        public int $maxTokens,
    ) {}
}
