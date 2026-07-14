<?php

declare(strict_types=1);

namespace App\Services\AI\DTOs;

final readonly class AiProviderResult
{
    /**
     * @param  list<AiToolCall>  $toolCalls
     */
    public function __construct(
        public string $content,
        public array $toolCalls = [],
        public ?int $promptTokens = null,
        public ?int $completionTokens = null,
    ) {}
}
