<?php

declare(strict_types=1);

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AiProviderInterface;
use App\Services\AI\DTOs\AiProviderRequest;
use App\Services\AI\DTOs\AiProviderResult;
use App\Services\AI\DTOs\AiToolCall;
use App\Services\AI\Exceptions\AiProviderException;
use Generator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use JsonException;
use Psr\Http\Message\StreamInterface;

final class LmStudioProvider implements AiProviderInterface
{
    public function name(): string
    {
        return 'lm_studio';
    }

    /**
     * @return Generator<int, string, mixed, AiProviderResult>
     */
    public function stream(AiProviderRequest $request): Generator
    {
        $configuration = config('ai.providers.lm_studio');
        $client = Http::baseUrl(rtrim((string) $configuration['base_url'], '/'))
            ->acceptJson()
            ->asJson()
            ->connectTimeout((int) $configuration['connect_timeout'])
            ->timeout((int) $configuration['timeout'])
            ->withOptions(['stream' => true]);

        $apiKey = trim((string) ($configuration['api_key'] ?? ''));
        if ($apiKey !== '') {
            $client = $client->withToken($apiKey);
        }

        $payload = [
            'model' => $request->model,
            'messages' => $request->messages,
            'temperature' => $request->temperature,
            'max_tokens' => $request->maxTokens,
            'stream' => true,
            'stream_options' => ['include_usage' => true],
        ];

        if ($request->tools !== []) {
            $payload['tools'] = $request->tools;
            $payload['tool_choice'] = 'auto';
        }

        try {
            $response = $client->post((string) $configuration['endpoint'], $payload);
        } catch (ConnectionException) {
            throw new AiProviderException(
                'LM Studio is unavailable. Start the local server and load the configured model.',
                'provider_unavailable',
            );
        }

        if (! $response->successful()) {
            throw new AiProviderException(
                'LM Studio rejected the request. Check that the configured model is loaded.',
                'provider_http_'.$response->status(),
            );
        }

        $content = '';
        $toolCalls = [];
        $promptTokens = null;
        $completionTokens = null;

        foreach ($this->events($response->toPsrResponse()->getBody()) as $event) {
            $usage = $event['usage'] ?? null;
            if (is_array($usage)) {
                $promptTokens = is_int($usage['prompt_tokens'] ?? null) ? $usage['prompt_tokens'] : $promptTokens;
                $completionTokens = is_int($usage['completion_tokens'] ?? null) ? $usage['completion_tokens'] : $completionTokens;
            }

            $delta = $event['choices'][0]['delta'] ?? null;
            if (! is_array($delta)) {
                continue;
            }

            $text = $delta['content'] ?? null;
            if (is_string($text) && $text !== '') {
                $content .= $text;
                yield $text;
            }

            foreach ($delta['tool_calls'] ?? [] as $fragment) {
                if (! is_array($fragment)) {
                    continue;
                }

                $index = (int) ($fragment['index'] ?? 0);
                $toolCalls[$index] ??= ['id' => '', 'name' => '', 'arguments' => ''];
                $toolCalls[$index]['id'] .= is_string($fragment['id'] ?? null) ? $fragment['id'] : '';

                $function = $fragment['function'] ?? null;
                if (is_array($function)) {
                    $toolCalls[$index]['name'] .= is_string($function['name'] ?? null) ? $function['name'] : '';
                    $toolCalls[$index]['arguments'] .= is_string($function['arguments'] ?? null) ? $function['arguments'] : '';
                }
            }
        }

        $normalizedToolCalls = [];
        foreach ($toolCalls as $toolCall) {
            if ($toolCall['id'] === '' || $toolCall['name'] === '') {
                throw new AiProviderException('LM Studio returned an invalid tool call.', 'invalid_tool_call');
            }

            $normalizedToolCalls[] = new AiToolCall(
                id: $toolCall['id'],
                name: $toolCall['name'],
                arguments: $toolCall['arguments'],
            );
        }

        return new AiProviderResult(
            content: $content,
            toolCalls: $normalizedToolCalls,
            promptTokens: $promptTokens,
            completionTokens: $completionTokens,
        );
    }

    /**
     * @return Generator<int, array<string, mixed>>
     */
    private function events(StreamInterface $body): Generator
    {
        $buffer = '';

        while (! $body->eof()) {
            $buffer .= $body->read(8192);

            while (preg_match('/\r?\n\r?\n/', $buffer, $match, PREG_OFFSET_CAPTURE) === 1) {
                $delimiter = $match[0][0];
                $offset = $match[0][1];
                $rawEvent = substr($buffer, 0, $offset);
                $buffer = substr($buffer, $offset + strlen($delimiter));

                $event = $this->decodeEvent($rawEvent);
                if ($event !== null) {
                    yield $event;
                }
            }
        }

        $event = $this->decodeEvent($buffer);
        if ($event !== null) {
            yield $event;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeEvent(string $rawEvent): ?array
    {
        $data = collect(preg_split('/\r?\n/', trim($rawEvent)) ?: [])
            ->filter(fn (string $line): bool => str_starts_with($line, 'data:'))
            ->map(fn (string $line): string => ltrim(substr($line, 5)))
            ->implode("\n");

        if ($data === '' || $data === '[DONE]') {
            return null;
        }

        try {
            $decoded = json_decode($data, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new AiProviderException('LM Studio returned an invalid streaming event.', 'invalid_stream');
        }

        if (! is_array($decoded)) {
            throw new AiProviderException('LM Studio returned an invalid response.', 'invalid_response');
        }

        return $decoded;
    }
}
