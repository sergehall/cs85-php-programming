<?php

namespace Tests\Feature;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AiRequest;
use App\Models\User;
use App\Services\AI\Contracts\AiProviderInterface;
use App\Services\AI\DTOs\AiProviderRequest;
use App\Services\AI\DTOs\AiProviderResult;
use App\Services\AI\DTOs\AiToolCall;
use App\Services\AI\Enums\AiMode;
use App\Services\AI\Exceptions\AiProviderException;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiAssistantTest extends TestCase
{
    use RefreshDatabase;

    private FakeAiProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->provider = new FakeAiProvider;
        $this->app->instance(AiProviderInterface::class, $this->provider);
    }

    public function test_user_and_admin_can_open_ai_workspace_but_guests_cannot(): void
    {
        $this->get('/cabinet/ai')->assertRedirect('/login');

        foreach (['user', 'admin'] as $role) {
            $response = $this->actingAs(User::factory()->create(['role' => $role]))->get('/cabinet/ai');

            $response->assertOk();
            $response->assertSee('AI Study Studio');
            $response->assertSee('General Tutor');
            $response->assertSee('Your private learning copilot');
            $response->assertSee('data-ai-conversation-search-empty', false);
            $response->assertDontSee('data-ai-model-guide', false);
            $response->assertDontSee('Specialized routing');
            $response->assertSee('Private history');
            $response->assertSee('Qwen 3.6 35B A3B');
            $response->assertSee('Qwen 3 Coder Next');
            $response->assertSee('OpenAI GPT-OSS 120B');
        }
    }

    public function test_user_can_create_a_mode_routed_conversation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/cabinet/ai/conversations', ['mode' => 'coding']);
        $conversation = AiConversation::query()->sole();

        $response->assertRedirect(route('cabinet.ai.conversations.show', $conversation->public_uuid));
        $this->assertTrue($conversation->user->is($user));
        $this->assertSame(AiMode::Coding, $conversation->mode);
        $this->assertSame('qwen/qwen3-coder-next', $conversation->model);

        $this->actingAs($user)
            ->get(route('cabinet.ai.conversations.show', $conversation->public_uuid))
            ->assertOk()
            ->assertSee('Coding Assistant')
            ->assertSee('Qwen 3 Coder Next')
            ->assertSee('Conversations and modes')
            ->assertSee('data-ai-message-form', false)
            ->assertSee('data-ai-character-count', false)
            ->assertSee('data-ai-scroll-latest', false)
            ->assertSee('Review this Laravel code for correctness and security.')
            ->assertSee('Local AI can make mistakes.');
    }

    public function test_conversation_mode_is_allowlisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/cabinet/ai')
            ->post('/cabinet/ai/conversations', ['mode' => 'untrusted-model'])
            ->assertRedirect('/cabinet/ai')
            ->assertSessionHasErrors('mode');

        $this->assertDatabaseCount('ai_conversations', 0);
    }

    public function test_streamed_reply_uses_multi_turn_history_and_is_persisted(): void
    {
        $user = User::factory()->create();
        $conversation = AiConversation::factory()->for($user)->create([
            'mode' => AiMode::General,
            'model' => 'qwen/qwen3.6-35b-a3b',
            'title' => 'New conversation',
        ]);
        AiMessage::factory()->for($conversation, 'conversation')->create([
            'role' => AiMessage::ROLE_USER,
            'content' => 'What is a controller?',
        ]);
        AiMessage::factory()->for($conversation, 'conversation')->create([
            'role' => AiMessage::ROLE_ASSISTANT,
            'content' => 'A controller handles an HTTP request.',
        ]);
        $this->provider->queue(['Local ', 'answer.'], new AiProviderResult('Local answer.', [], 20, 8));

        $response = $this->actingAs($user)->post(
            route('cabinet.ai.conversations.messages.stream', $conversation->public_uuid),
            ['message' => 'How does that fit in Laravel?'],
            ['Accept' => 'text/event-stream'],
        );

        $response->assertOk()->assertHeader('Content-Type', 'text/event-stream; charset=UTF-8');
        $streamed = $response->streamedContent();

        $this->assertStringContainsString('Local ', $streamed);
        $this->assertStringContainsString('answer.', $streamed);
        $this->assertStringContainsString('"rendered_html":"<p>Local</p>', $streamed);
        $this->assertStringContainsString('event: complete', $streamed);
        $this->assertCount(1, $this->provider->requests);
        $this->assertSame(
            ['system', 'user', 'assistant', 'user'],
            collect($this->provider->requests[0]->messages)->pluck('role')->all(),
        );
        $this->assertDatabaseHas('ai_messages', [
            'ai_conversation_id' => $conversation->getKey(),
            'role' => AiMessage::ROLE_ASSISTANT,
            'content' => 'Local answer.',
        ]);
        $this->assertDatabaseHas('ai_requests', [
            'user_message_id' => AiMessage::query()->where('content', 'How does that fit in Laravel?')->value('id'),
            'user_id' => $user->getKey(),
            'status' => AiRequest::STATUS_COMPLETED,
            'prompt_tokens' => 20,
            'completion_tokens' => 8,
        ]);
        $this->assertDatabaseHas('activity_logs', ['event' => 'ai.response.completed']);
    }

    public function test_allowlisted_tool_call_gets_one_follow_up_round(): void
    {
        $user = User::factory()->create();
        $conversation = AiConversation::factory()->for($user)->create([
            'title' => 'New conversation',
        ]);
        $this->provider->queue([], new AiProviderResult('', [
            new AiToolCall('call-1', 'get_course_module', '{"module":"module-8"}'),
        ], 10, 4));
        $this->provider->queue(['Module 8 uses Eloquent.'], new AiProviderResult('Module 8 uses Eloquent.', [], 18, 6));

        $response = $this->actingAs($user)->post(
            route('cabinet.ai.conversations.messages.stream', $conversation->public_uuid),
            ['message' => 'What is in module 8?'],
            ['Accept' => 'text/event-stream'],
        );

        $streamed = $response->streamedContent();

        $this->assertStringContainsString('event: status', $streamed);
        $this->assertStringContainsString('Module 8 uses Eloquent.', $streamed);
        $this->assertCount(2, $this->provider->requests);
        $this->assertSame([], $this->provider->requests[1]->tools);
        $this->assertContains('tool', collect($this->provider->requests[1]->messages)->pluck('role')->all());
    }

    public function test_conversations_are_private_to_their_owner_and_output_is_escaped(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $conversation = AiConversation::factory()->for($owner)->create([
            'title' => str_repeat('Long conversation title ', 8),
        ]);
        AiMessage::factory()->for($conversation, 'conversation')->create([
            'role' => AiMessage::ROLE_ASSISTANT,
            'content' => "## Safe heading\n\n**Formatted answer**\n\n<script>alert(\"ai\")</script> ".str_repeat('unbroken-token-', 80),
        ]);

        $this->actingAs($otherUser)
            ->get(route('cabinet.ai.conversations.show', $conversation->public_uuid))
            ->assertNotFound();
        $this->actingAs($otherUser)
            ->delete(route('cabinet.ai.conversations.destroy', $conversation->public_uuid))
            ->assertNotFound();

        $response = $this->actingAs($owner)->get(route('cabinet.ai.conversations.show', $conversation->public_uuid));
        $response->assertOk();
        $response->assertSee('data-ai-copy', false);
        $response->assertSee('data-ai-conversation-title', false);
        $response->assertSee('[overflow-wrap:anywhere]', false);
        $response->assertSee('overflow-x-hidden', false);
        $response->assertDontSee('<script>alert("ai")</script>', false);
        $response->assertSee('<h2>Safe heading</h2>', false);
        $response->assertSee('<strong>Formatted answer</strong>', false);
    }

    public function test_provider_failure_is_safe_and_recorded_without_prompt_content(): void
    {
        $user = User::factory()->create();
        $conversation = AiConversation::factory()->for($user)->create([
            'title' => 'New conversation',
        ]);
        $this->provider->fail = true;

        $response = $this->actingAs($user)->post(
            route('cabinet.ai.conversations.messages.stream', $conversation->public_uuid),
            ['message' => 'secret classroom draft'],
            ['Accept' => 'text/event-stream'],
        );

        $streamed = $response->streamedContent();

        $this->assertStringContainsString('event: error', $streamed);
        $this->assertStringNotContainsString('secret classroom draft', $streamed);
        $userMessage = AiMessage::query()->where('role', AiMessage::ROLE_USER)->sole();
        $this->assertDatabaseHas('ai_requests', [
            'user_message_id' => $userMessage->getKey(),
            'status' => AiRequest::STATUS_FAILED,
            'error_code' => 'provider_unavailable',
        ]);
        $this->assertSame('secret classroom draft', $conversation->refresh()->title);

        $this->actingAs($user)
            ->get(route('cabinet.ai.conversations.show', $conversation->public_uuid))
            ->assertOk()
            ->assertSee('Response failed.')
            ->assertSee('data-ai-retry-form', false);
    }

    public function test_failed_message_can_be_retried_without_duplicating_the_prompt(): void
    {
        $user = User::factory()->create();
        $conversation = AiConversation::factory()->for($user)->create([
            'title' => 'New conversation',
        ]);
        $this->provider->fail = true;

        $this->actingAs($user)->post(
            route('cabinet.ai.conversations.messages.stream', $conversation->public_uuid),
            ['message' => 'Explain service containers.'],
            ['Accept' => 'text/event-stream'],
        )->streamedContent();

        $userMessage = AiMessage::query()->where('role', AiMessage::ROLE_USER)->sole();
        $this->provider->fail = false;
        $this->provider->queue(
            ['Recovered **answer**.'],
            new AiProviderResult('Recovered **answer**.', [], 12, 4),
        );

        $response = $this->actingAs($user)->post(
            route('cabinet.ai.conversations.messages.retry', [
                $conversation->public_uuid,
                $userMessage->getKey(),
            ]),
            [],
            ['Accept' => 'text/event-stream'],
        );

        $streamed = $response->streamedContent();

        $response->assertOk();
        $this->assertStringContainsString('event: complete', $streamed);
        $this->assertStringContainsString('<strong>answer</strong>', $streamed);
        $this->assertDatabaseCount('ai_messages', 2);
        $this->assertDatabaseCount('ai_requests', 2);
        $this->assertDatabaseHas('ai_requests', [
            'user_message_id' => $userMessage->getKey(),
            'status' => AiRequest::STATUS_COMPLETED,
        ]);
    }
}

final class FakeAiProvider implements AiProviderInterface
{
    /** @var list<AiProviderRequest> */
    public array $requests = [];

    /** @var list<array{chunks:list<string>,result:AiProviderResult}> */
    private array $responses = [];

    public bool $fail = false;

    /**
     * @param  list<string>  $chunks
     */
    public function queue(array $chunks, AiProviderResult $result): void
    {
        $this->responses[] = ['chunks' => $chunks, 'result' => $result];
    }

    public function name(): string
    {
        return 'fake';
    }

    public function stream(AiProviderRequest $request): Generator
    {
        $this->requests[] = $request;

        if ($this->fail) {
            throw new AiProviderException('LM Studio is unavailable.', 'provider_unavailable');
        }

        $response = array_shift($this->responses) ?? [
            'chunks' => ['Default response.'],
            'result' => new AiProviderResult('Default response.'),
        ];

        foreach ($response['chunks'] as $chunk) {
            yield $chunk;
        }

        return $response['result'];
    }
}
