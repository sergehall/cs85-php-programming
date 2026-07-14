<?php

declare(strict_types=1);

namespace App\Services\AI\Contracts;

use App\Services\AI\DTOs\AiProviderRequest;
use App\Services\AI\DTOs\AiProviderResult;
use Generator;

interface AiProviderInterface
{
    /**
     * @return Generator<int, string, mixed, AiProviderResult>
     */
    public function stream(AiProviderRequest $request): Generator;

    public function name(): string;
}
