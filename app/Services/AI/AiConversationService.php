<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\ActivityLog;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AiRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\AI\Contracts\AiProviderInterface;
use App\Services\AI\DTOs\AiProviderRequest;
use App\Services\AI\DTOs\AiProviderResult;
use App\Services\AI\Enums\AiMode;
use App\Services\AI\Exceptions\AiProviderException;
use Generator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;
use Throwable;

final class AiConversationService
{
    public function __construct(
        private readonly AiProviderInterface $provider,
        private readonly ModelRouter $router,
        private readonly PromptBuilder $prompts,
        private readonly AiToolRegistry $tools,
        private readonly ActivityLogger $activity,
    ) {}

    public function createConversation(User $user, AiMode $mode): AiConversation
    {
        return $user->aiConversations()->create([
            'title' => 'New conversation',
            'mode' => $mode,
            'model' => $this->router->model($mode),
        ]);
    }

    /**
     * @return Generator<int, array<string, mixed>>
     */
    public function streamReply(AiConversation $conversation, string $prompt): Generator
    {
        $mode = $conversation->mode;
        $modeConfiguration = $this->router->configuration($mode);
        $userMessage = $conversation->messages()->create([
            'role' => AiMessage::ROLE_USER,
            'content' => $prompt,
        ]);

        $requestLog = AiRequest::query()->create([
            'ai_conversation_id' => $conversation->getKey(),
            'user_id' => $conversation->user_id,
            'mode' => $mode->value,
            'provider' => $this->provider->name(),
            'model' => $conversation->model,
            'status' => AiRequest::STATUS_PROCESSING,
        ]);

        $startedAt = hrtime(true);

        try {
            $messages = $this->conversationMessages($conversation, $mode);
            $providerStream = $this->provider->stream(new AiProviderRequest(
                model: $conversation->model,
                messages: $messages,
                tools: $this->tools->definitions(),
                temperature: (float) $modeConfiguration['temperature'],
                maxTokens: (int) config('ai.limits.max_output_tokens'),
            ));

            foreach ($providerStream as $delta) {
                yield ['type' => 'delta', 'content' => $delta];
            }

            $result = $providerStream->getReturn();
            $assistantContent = $result->content;
            $promptTokens = $result->promptTokens;
            $completionTokens = $result->completionTokens;

            if ($result->toolCalls !== []) {
                yield ['type' => 'status', 'content' => 'Reading approved course context…'];

                $messages[] = $this->assistantToolCallMessage($result);
                foreach ($result->toolCalls as $toolCall) {
                    $toolResult = $this->tools->execute($toolCall);
                    $messages[] = [
                        'role' => AiMessage::ROLE_TOOL,
                        'tool_call_id' => $toolCall->id,
                        'content' => json_encode($toolResult, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
                    ];
                }

                $followUpStream = $this->provider->stream(new AiProviderRequest(
                    model: $conversation->model,
                    messages: $messages,
                    tools: [],
                    temperature: (float) $modeConfiguration['temperature'],
                    maxTokens: (int) config('ai.limits.max_output_tokens'),
                ));

                if ($assistantContent !== '') {
                    yield ['type' => 'delta', 'content' => "\n\n"];
                }

                foreach ($followUpStream as $delta) {
                    yield ['type' => 'delta', 'content' => $delta];
                }

                $followUpResult = $followUpStream->getReturn();
                $assistantContent = trim($assistantContent) === ''
                    ? $followUpResult->content
                    : rtrim($assistantContent)."\n\n".$followUpResult->content;
                $promptTokens = $this->sumTokens($promptTokens, $followUpResult->promptTokens);
                $completionTokens = $this->sumTokens($completionTokens, $followUpResult->completionTokens);
            }

            if (trim($assistantContent) === '') {
                throw new AiProviderException('LM Studio returned an empty response.', 'empty_response');
            }

            $assistantMessage = $conversation->messages()->create([
                'role' => AiMessage::ROLE_ASSISTANT,
                'content' => $assistantContent,
                'metadata' => [
                    'provider' => $this->provider->name(),
                    'model' => $conversation->model,
                ],
            ]);

            if ($conversation->title === 'New conversation') {
                $conversation->title = $this->titleFromPrompt($prompt);
            }

            $conversation->touch();
            $conversation->save();

            $requestLog->update([
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'latency_ms' => $this->elapsedMilliseconds($startedAt),
                'status' => AiRequest::STATUS_COMPLETED,
                'error_code' => null,
            ]);

            $user = $conversation->user;
            if ($user instanceof User) {
                $this->activity->record(
                    subject: $user,
                    actor: $user,
                    category: 'ai',
                    event: 'ai.response.completed',
                    title: 'AI response completed',
                    description: 'A local AI conversation received a response.',
                    visibility: ActivityLog::VISIBILITY_USER,
                    metadata: [
                        'mode' => $mode->value,
                        'provider' => $this->provider->name(),
                        'model' => $conversation->model,
                    ],
                );
            }

            yield [
                'type' => 'complete',
                'message_id' => $assistantMessage->getKey(),
                'conversation_title' => $conversation->title,
                'user_message_id' => $userMessage->getKey(),
            ];
        } catch (Throwable $exception) {
            $errorCode = $exception instanceof AiProviderException
                ? $exception->errorCode
                : ($exception instanceof JsonException ? 'invalid_tool_result' : 'application_error');
            $message = $exception instanceof AiProviderException
                ? $exception->getMessage()
                : 'The local AI request could not be completed. Please try again.';

            $requestLog->update([
                'latency_ms' => $this->elapsedMilliseconds($startedAt),
                'status' => AiRequest::STATUS_FAILED,
                'error_code' => $errorCode,
            ]);

            Log::warning('Local AI request failed.', [
                'ai_request_id' => $requestLog->getKey(),
                'error_code' => $errorCode,
                'exception' => $exception::class,
            ]);

            yield ['type' => 'error', 'content' => $message, 'error_code' => $errorCode];
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function conversationMessages(AiConversation $conversation, AiMode $mode): array
    {
        $historyLimit = max(2, (int) config('ai.limits.history_messages'));
        $history = $conversation->messages()
            ->whereIn('role', [AiMessage::ROLE_USER, AiMessage::ROLE_ASSISTANT])
            ->latest()
            ->limit($historyLimit)
            ->get()
            ->reverse()
            ->values();

        return [
            ['role' => 'system', 'content' => $this->prompts->systemPrompt($mode)],
            ...$history->map(fn (AiMessage $message): array => [
                'role' => $message->role,
                'content' => $message->content,
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function assistantToolCallMessage(AiProviderResult $result): array
    {
        return [
            'role' => AiMessage::ROLE_ASSISTANT,
            'content' => $result->content !== '' ? $result->content : null,
            'tool_calls' => collect($result->toolCalls)->map(fn ($toolCall): array => [
                'id' => $toolCall->id,
                'type' => 'function',
                'function' => [
                    'name' => $toolCall->name,
                    'arguments' => $toolCall->arguments,
                ],
            ])->all(),
        ];
    }

    private function sumTokens(?int $first, ?int $second): ?int
    {
        return $first === null && $second === null ? null : ($first ?? 0) + ($second ?? 0);
    }

    private function elapsedMilliseconds(int $startedAt): int
    {
        return max(0, (int) round((hrtime(true) - $startedAt) / 1_000_000));
    }

    private function titleFromPrompt(string $prompt): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($prompt)) ?? 'New conversation';

        return Str::limit($normalized, 80, '…');
    }
}
