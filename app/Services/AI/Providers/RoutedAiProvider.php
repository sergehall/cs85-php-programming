<?php

declare(strict_types=1);

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AiProviderInterface;
use App\Services\AI\DTOs\AiProviderRequest;
use App\Services\AI\DTOs\AiProviderResult;
use App\Services\AI\Exceptions\AiProviderException;
use Generator;

final class RoutedAiProvider implements AiProviderInterface
{
    public function __construct(
        private readonly LmStudioProvider $lmStudio,
        private readonly OpenAiProvider $openAi,
    ) {}

    public function name(): string
    {
        return 'routed';
    }

    /**
     * @return Generator<int, string, mixed, AiProviderResult>
     */
    public function stream(AiProviderRequest $request): Generator
    {
        $provider = match ($request->provider) {
            'lm_studio' => $this->lmStudio,
            'openai' => $this->openAi,
            default => throw new AiProviderException(
                'The selected AI provider is not supported.',
                'provider_not_supported',
            ),
        };

        return yield from $provider->stream($request);
    }
}
