<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class AiContentService
{
    /**
     * Generate a draft from a title, content type, and tone.
     */
    public function generateDraft(
        string $title,
        string $type = 'blog post',
        string $tone = 'professional',
    ): string {
        $apiKey = trim((string) config('services.openai.key'));

        if ($apiKey === '') {
            throw new RuntimeException('The OpenAI API key is not configured.');
        }

        $prompt = $this->buildPrompt($title, $type, $tone);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->connectTimeout(max(1, (int) config('services.openai.connect_timeout', 5)))
            ->timeout(max(1, (int) config('services.openai.timeout', 30)))
            ->post($this->endpoint(), [
                'model' => (string) config('services.openai.model', 'gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => $this->roleForTone($tone)],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

        $this->throwWhenFailed($response);

        $content = $response->json('choices.0.message.content');

        return is_string($content) && trim($content) !== ''
            ? trim($content)
            : 'No output received';
    }

    /**
     * Build the content-specific prompt sent to OpenAI.
     */
    private function buildPrompt(string $title, string $type, string $tone): string
    {
        $normalizedTitle = preg_replace('/\s+/u', ' ', trim($title)) ?? trim($title);
        [$task, $format] = match ($type) {
            'blog post' => [
                "Write a complete blog post titled: \"{$normalizedTitle}\".",
                'Write 350-500 words in clean Markdown with an engaging introduction, 2-3 descriptive headings, practical details, and a concise conclusion.',
            ],
            'meta description' => [
                "Write a meta description for a page titled: \"{$normalizedTitle}\".",
                'Return one compelling line of approximately 150-160 characters. Do not add quotation marks, labels, or hashtags.',
            ],
            'email subject line' => [
                "Write an email subject line about: \"{$normalizedTitle}\".",
                'Return one clear subject line of 6-10 words and no more than 60 characters. Do not add quotation marks or a label.',
            ],
            default => throw new InvalidArgumentException("Unsupported content type [{$type}]."),
        };
        $toneInstruction = match ($tone) {
            'professional' => 'Use a polished, credible, and precise professional tone.',
            'casual' => 'Use a friendly, natural, and approachable conversational tone.',
            'humorous' => 'Use a witty, playful tone while keeping the message useful and respectful.',
            default => throw new InvalidArgumentException("Unsupported tone [{$tone}]."),
        };

        return implode("\n\n", [
            "Task: {$task}",
            "Tone: {$toneInstruction}",
            "Format: {$format}",
            'Return only the requested draft so the user can edit it directly.',
        ]);
    }

    private function roleForTone(string $tone): string
    {
        return match ($tone) {
            'professional' => 'You are a professional content strategist who writes clear, accurate, publication-ready copy.',
            'casual' => 'You are a friendly digital copywriter who makes ideas easy to understand and enjoyable to read.',
            'humorous' => 'You are a witty copywriter who uses light humor without sacrificing clarity or accuracy.',
            default => throw new InvalidArgumentException("Unsupported tone [{$tone}]."),
        };
    }

    private function endpoint(): string
    {
        $baseUrl = rtrim((string) config('services.openai.url', 'https://api.openai.com/v1'), '/');

        return "{$baseUrl}/chat/completions";
    }

    private function throwWhenFailed(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        Log::error('OpenAI content generation failed.', [
            'status' => $response->status(),
            'body' => Str::limit($response->body(), 1000),
        ]);

        throw new RuntimeException('The AI content request failed.');
    }
}
