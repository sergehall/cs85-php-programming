<?php

namespace Tests\Unit\AI;

use App\Services\AI\Contracts\AiProviderInterface;
use App\Services\AI\DTOs\AiProviderRequest;
use App\Services\AI\Providers\RoutedAiProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RoutedAiProviderTest extends TestCase
{
    public function test_application_provider_routes_an_online_request_to_openai(): void
    {
        config([
            'ai.providers.openai.base_url' => 'https://openai.test/v1',
            'ai.providers.openai.api_key' => 'secret-test-key',
        ]);
        Http::fake([
            'https://openai.test/v1/chat/completions' => Http::response(implode("\n\n", [
                'data: {"choices":[{"delta":{"content":"Routed online."}}]}',
                'data: [DONE]',
                '',
            ])),
        ]);

        $provider = app(AiProviderInterface::class);
        $this->assertInstanceOf(RoutedAiProvider::class, $provider);

        $stream = $provider->stream(new AiProviderRequest(
            provider: 'openai',
            model: 'gpt-4o-mini',
            messages: [['role' => 'user', 'content' => 'Hello']],
            tools: [],
            temperature: 0.4,
            maxTokens: 100,
        ));

        $this->assertSame(['Routed online.'], iterator_to_array($stream, false));
        $this->assertSame('Routed online.', $stream->getReturn()->content);
    }
}
