<?php

namespace Tests\Unit\AI;

use App\Services\AI\DTOs\AiProviderRequest;
use App\Services\AI\Providers\LmStudioProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LmStudioProviderTest extends TestCase
{
    public function test_provider_parses_streamed_text_usage_and_tool_call_fragments(): void
    {
        config([
            'ai.providers.lm_studio.base_url' => 'http://lm-studio.test/v1',
            'ai.providers.lm_studio.api_key' => 'local-key',
        ]);

        Http::fake([
            'http://lm-studio.test/v1/chat/completions' => Http::response(implode("\n\n", [
                'data: {"choices":[{"delta":{"content":"Hello "}}]}',
                'data: {"choices":[{"delta":{"content":"student"}}]}',
                'data: {"choices":[{"delta":{"tool_calls":[{"index":0,"id":"call-1","function":{"name":"get_course_","arguments":"{\\"module\\":"}}]}}]}',
                'data: {"choices":[{"delta":{"tool_calls":[{"index":0,"function":{"name":"module","arguments":"\\"module-8\\"}"}}]}}]}',
                'data: {"choices":[],"usage":{"prompt_tokens":12,"completion_tokens":7}}',
                'data: [DONE]',
                '',
            ]), 200, ['Content-Type' => 'text/event-stream']),
        ]);

        $stream = app(LmStudioProvider::class)->stream(new AiProviderRequest(
            model: 'test-model',
            messages: [['role' => 'user', 'content' => 'Hello']],
            tools: [['type' => 'function', 'function' => ['name' => 'get_course_module']]],
            temperature: 0.2,
            maxTokens: 100,
        ));

        $chunks = iterator_to_array($stream, false);
        $result = $stream->getReturn();

        $this->assertSame(['Hello ', 'student'], $chunks);
        $this->assertSame('Hello student', $result->content);
        $this->assertSame(12, $result->promptTokens);
        $this->assertSame(7, $result->completionTokens);
        $this->assertCount(1, $result->toolCalls);
        $this->assertSame('get_course_module', $result->toolCalls[0]->name);
        $this->assertSame('{"module":"module-8"}', $result->toolCalls[0]->arguments);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'http://lm-studio.test/v1/chat/completions'
                && $request['stream'] === true
                && $request['model'] === 'test-model'
                && $request->hasHeader('Authorization', 'Bearer local-key');
        });
    }
}
