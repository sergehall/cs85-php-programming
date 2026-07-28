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
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use JsonException;
use Psr\Http\Message\StreamInterface;

abstract class OpenAiCompatibleProvider implements AiProviderInterface
{
    abstract public function displayName(): string;

    abstract protected function requiresApiKey(): bool;

    /**
     * @return Generator<int, string, mixed, AiProviderResult>
     */
    public function stream(AiProviderRequest $request): Generator
    {
        $configuration = $this->configuration();
        $apiKey = trim((string) ($configuration['api_key'] ?? ''));

        if ($this->requiresApiKey() && $apiKey === '') {
            throw new AiProviderException(
                "{$this->displayName()} is not configured. Add its API key to the server environment.",
                'provider_not_configured',
            );
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
            $response = $this->client(stream: true)
                ->post((string) $configuration['endpoint'], $payload);
        } catch (ConnectionException) {
            throw new AiProviderException(
                "{$this->displayName()} is unavailable. Check the provider connection and try again.",
                'provider_unavailable',
            );
        }

        if (! $response->successful()) {
            throw new AiProviderException(
                "{$this->displayName()} rejected the request. Check the configured model and provider access.",
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
                throw new AiProviderException(
                    "{$this->displayName()} returned an invalid tool call.",
                    'invalid_tool_call',
                );
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
     * @param  array<string, array{label:string,model:string,model_name:string}>  $modes
     * @return array{
     *     provider:string,
     *     label:string,
     *     location:string,
     *     reachable:bool,
     *     configured:bool,
     *     latency_ms:int|null,
     *     message:string,
     *     models:list<array{mode:string,label:string,model:string,model_name:string,connected:bool,status:string,message:string}>
     * }
     */
    public function inspectModels(array $modes): array
    {
        $configuration = $this->configuration();
        $apiKey = trim((string) ($configuration['api_key'] ?? ''));

        if ($this->requiresApiKey() && $apiKey === '') {
            return $this->inspectionResult(
                modes: $modes,
                reachable: false,
                configured: false,
                latencyMs: null,
                availableModels: [],
                message: 'API key is not configured on the Laravel server.',
                unavailableStatus: 'not_configured',
            );
        }

        $startedAt = hrtime(true);

        try {
            $response = $this->client()->get('/models');
        } catch (ConnectionException) {
            return $this->inspectionResult(
                modes: $modes,
                reachable: false,
                configured: true,
                latencyMs: $this->elapsedMilliseconds($startedAt),
                availableModels: [],
                message: 'Provider endpoint is unreachable.',
                unavailableStatus: 'unreachable',
            );
        }

        if (! $response->successful()) {
            return $this->inspectionResult(
                modes: $modes,
                reachable: false,
                configured: true,
                latencyMs: $this->elapsedMilliseconds($startedAt),
                availableModels: [],
                message: "Provider health check returned HTTP {$response->status()}.",
                unavailableStatus: 'rejected',
            );
        }

        $availableModels = collect($response->json('data', []))
            ->filter(fn (mixed $model): bool => is_array($model) && is_string($model['id'] ?? null))
            ->pluck('id')
            ->all();

        return $this->inspectionResult(
            modes: $modes,
            reachable: true,
            configured: true,
            latencyMs: $this->elapsedMilliseconds($startedAt),
            availableModels: $availableModels,
            message: 'Provider authenticated and model catalog received.',
            unavailableStatus: 'missing',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function configuration(): array
    {
        $configuration = config("ai.providers.{$this->name()}");

        if (! is_array($configuration)) {
            throw new AiProviderException(
                "{$this->displayName()} configuration is missing.",
                'provider_not_configured',
            );
        }

        return $configuration;
    }

    private function client(bool $stream = false): PendingRequest
    {
        $configuration = $this->configuration();
        $client = Http::baseUrl(rtrim((string) $configuration['base_url'], '/'))
            ->acceptJson()
            ->asJson()
            ->connectTimeout(max(1, (int) $configuration['connect_timeout']))
            ->timeout(max(1, (int) $configuration['timeout']));

        if ($stream) {
            $client = $client->withOptions(['stream' => true]);
        }

        $apiKey = trim((string) ($configuration['api_key'] ?? ''));

        return $apiKey !== '' ? $client->withToken($apiKey) : $client;
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
            throw new AiProviderException(
                "{$this->displayName()} returned an invalid streaming event.",
                'invalid_stream',
            );
        }

        if (! is_array($decoded)) {
            throw new AiProviderException(
                "{$this->displayName()} returned an invalid response.",
                'invalid_response',
            );
        }

        return $decoded;
    }

    /**
     * @param  array<string, array{label:string,model:string,model_name:string}>  $modes
     * @param  list<string>  $availableModels
     * @return array{
     *     provider:string,
     *     label:string,
     *     location:string,
     *     reachable:bool,
     *     configured:bool,
     *     latency_ms:int|null,
     *     message:string,
     *     models:list<array{mode:string,label:string,model:string,model_name:string,connected:bool,status:string,message:string}>
     * }
     */
    private function inspectionResult(
        array $modes,
        bool $reachable,
        bool $configured,
        ?int $latencyMs,
        array $availableModels,
        string $message,
        string $unavailableStatus,
    ): array {
        $models = [];

        foreach ($modes as $mode => $configuration) {
            $connected = $reachable && in_array($configuration['model'], $availableModels, true);
            $models[] = [
                'mode' => $mode,
                'label' => $configuration['label'],
                'model' => $configuration['model'],
                'model_name' => $configuration['model_name'],
                'connected' => $connected,
                'status' => $connected ? 'connected' : $unavailableStatus,
                'message' => $connected
                    ? "{$configuration['model_name']} is available through {$this->displayName()}."
                    : ($reachable
                        ? "{$configuration['model_name']} is not listed by {$this->displayName()}."
                        : $message),
            ];
        }

        return [
            'provider' => $this->name(),
            'label' => (string) config("ai.providers.{$this->name()}.label", $this->displayName()),
            'location' => (string) config("ai.providers.{$this->name()}.location", 'Unknown'),
            'reachable' => $reachable,
            'configured' => $configured,
            'latency_ms' => $latencyMs,
            'message' => $message,
            'models' => $models,
        ];
    }

    private function elapsedMilliseconds(int $startedAt): int
    {
        return max(0, (int) round((hrtime(true) - $startedAt) / 1_000_000));
    }
}
