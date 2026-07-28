<?php

namespace Tests\Unit\AI;

use App\Services\AI\DTOs\AiProviderRequest;
use App\Services\AI\Providers\OpenAiProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenAiProviderTest extends TestCase
{
    public function test_provider_streams_online_openai_content_with_server_side_credentials(): void
    {
        config([
            'ai.providers.openai.base_url' => 'https://openai.test/v1',
            'ai.providers.openai.api_key' => 'secret-test-key',
        ]);

        Http::fake([
            'https://openai.test/v1/chat/completions' => Http::response(implode("\n\n", [
                'data: {"choices":[{"delta":{"content":"Online "}}]}',
                'data: {"choices":[{"delta":{"content":"answer."}}]}',
                'data: {"choices":[],"usage":{"prompt_tokens":9,"completion_tokens":3}}',
                'data: [DONE]',
                '',
            ]), 200, ['Content-Type' => 'text/event-stream']),
        ]);

        $stream = app(OpenAiProvider::class)->stream(new AiProviderRequest(
            provider: 'openai',
            model: 'gpt-4o-mini',
            messages: [['role' => 'user', 'content' => 'Help me study.']],
            tools: [],
            temperature: 0.4,
            maxTokens: 200,
        ));

        $this->assertSame(['Online ', 'answer.'], iterator_to_array($stream, false));
        $this->assertSame('Online answer.', $stream->getReturn()->content);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://openai.test/v1/chat/completions'
            && $request['model'] === 'gpt-4o-mini'
            && $request->hasHeader('Authorization', 'Bearer secret-test-key'));
    }

    public function test_health_check_reports_missing_key_without_making_a_network_request(): void
    {
        config(['ai.providers.openai.api_key' => '']);
        Http::preventStrayRequests();

        $status = app(OpenAiProvider::class)->inspectModels([
            'online' => [
                'label' => 'OpenAI Online',
                'model' => 'gpt-4o-mini',
                'model_name' => 'OpenAI GPT-4o mini',
            ],
        ]);

        $this->assertFalse($status['configured']);
        $this->assertFalse($status['reachable']);
        $this->assertSame('not_configured', $status['models'][0]['status']);
        $this->assertStringNotContainsString('secret', $status['message']);
        Http::assertNothingSent();
    }
}
