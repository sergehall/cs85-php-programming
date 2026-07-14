<?php

declare(strict_types=1);

namespace App\Services\AI\DTOs;

final readonly class AiToolCall
{
    public function __construct(
        public string $id,
        public string $name,
        public string $arguments,
    ) {}
}
