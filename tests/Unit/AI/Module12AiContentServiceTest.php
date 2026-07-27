<?php

declare(strict_types=1);

namespace Tests\Unit\AI;

use App\Services\AiContentService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

final class Module12AiContentServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.openai', [
            'key' => 'test-openai-key',
            'url' => 'https://api.openai.com/v1',
            'model' => 'gpt-4o-mini',
            'connect_timeout' => 5,
            'timeout' => 30,
        ]);
    }

    public function test_service_calls_openai_and_returns_the_assistant_content(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '  Generated blog draft.  ']],
                ],
            ]),
        ]);

        $result = app(AiContentService::class)->generateDraft(
            'Laravel service architecture',
            'blog post',
            'professional',
        );

        $this->assertSame('Generated blog draft.', $result);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();
            $messages = $data['messages'] ?? [];

            return $request->url() === 'https://api.openai.com/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer test-openai-key')
                && $request->hasHeader('Accept', 'application/json')
                && ($data['model'] ?? null) === 'gpt-4o-mini'
                && ($data['temperature'] ?? null) === 0.7
                && ($data['max_tokens'] ?? null) === 500
                && str_contains((string) ($messages[0]['content'] ?? ''), 'professional content strategist')
                && str_contains((string) ($messages[1]['content'] ?? ''), '350-500 words')
                && str_contains((string) ($messages[1]['content'] ?? ''), 'Laravel service architecture');
        });
    }

    public function test_prompt_adapts_to_meta_description_and_humorous_tone(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'A concise meta description.']],
                ],
            ]),
        ]);

        app(AiContentService::class)->generateDraft(
            'Testing Laravel applications',
            'meta description',
            'humorous',
        );

        Http::assertSent(function (Request $request): bool {
            $messages = $request->data()['messages'] ?? [];
            $system = (string) ($messages[0]['content'] ?? '');
            $prompt = (string) ($messages[1]['content'] ?? '');

            return str_contains($system, 'witty copywriter')
                && str_contains($prompt, 'approximately 150-160 characters')
                && str_contains($prompt, 'witty, playful tone')
                && str_contains($prompt, 'Testing Laravel applications');
        });
    }

    public function test_prompt_adapts_to_email_subject_and_casual_tone(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Build Better Laravel Services Today']],
                ],
            ]),
        ]);

        app(AiContentService::class)->generateDraft(
            'Laravel service patterns',
            'email subject line',
            'casual',
        );

        Http::assertSent(function (Request $request): bool {
            $messages = $request->data()['messages'] ?? [];
            $system = (string) ($messages[0]['content'] ?? '');
            $prompt = (string) ($messages[1]['content'] ?? '');

            return str_contains($system, 'friendly digital copywriter')
                && str_contains($prompt, '6-10 words')
                && str_contains($prompt, 'no more than 60 characters')
                && str_contains($prompt, 'friendly, natural');
        });
    }

    public function test_missing_content_uses_the_assignment_fallback(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['choices' => []]),
        ]);

        $result = app(AiContentService::class)->generateDraft(
            'Missing provider content',
            'blog post',
            'professional',
        );

        $this->assertSame('No output received', $result);
    }

    public function test_failed_openai_response_throws_a_safe_exception(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response(
                ['error' => ['message' => 'Rate limit reached']],
                429,
            ),
        ]);

        try {
            app(AiContentService::class)->generateDraft(
                'Provider failure handling',
                'blog post',
                'professional',
            );

            $this->fail('The service should throw when OpenAI returns a failed response.');
        } catch (RuntimeException $exception) {
            $this->assertSame('The AI content request failed.', $exception->getMessage());
        }
    }

    public function test_missing_api_key_fails_before_an_http_request(): void
    {
        config()->set('services.openai.key', '');
        Http::fake();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The OpenAI API key is not configured.');

        try {
            app(AiContentService::class)->generateDraft(
                'Missing API key',
                'blog post',
                'professional',
            );
        } finally {
            Http::assertNothingSent();
        }
    }
}
