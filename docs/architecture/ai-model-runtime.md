# AI Model Runtime

This document is the implementation contract for selecting and calling AI
models in the Laravel application. The source of truth for provider and model
configuration is [`config/ai.php`](../../config/ai.php).

## Runtime Responsibilities

The integration has four clear boundaries:

```text
Authenticated browser
    -> Laravel AI controller and conversation service
        -> AI provider interface
            -> LM Studio OpenAI-compatible API
                -> configured local model
```

- The browser creates conversations, submits messages, and consumes a
  same-origin Server-Sent Events (SSE) response.
- Laravel owns authentication, authorization, conversation history, prompts,
  model routing, tool execution, Markdown rendering, retries, and telemetry.
- `AiProviderInterface` isolates application logic from provider-specific HTTP
  behavior.
- LM Studio performs local inference. It does not own application sessions or
  conversation history.

The browser never calls LM Studio directly and never receives a provider
credential.

## Mode-to-Model Mapping

Users select a task-oriented mode, not an arbitrary model identifier.
`ModelRouter` resolves the mode through `config/ai.php`.

| Mode           | UI model name       | Provider model identifier | Current profile           | Temperature | Intended work                                                      |
| -------------- | ------------------- | ------------------------- | ------------------------- | ----------- | ------------------------------------------------------------------ |
| `general`      | Qwen 3.6 35B A3B    | `qwen/qwen3.6-35b-a3b`    | 35B MoE · 4-bit · 20.4 GB | `0.4`       | Learning, explanations, quizzes, and general programming questions |
| `coding`       | Qwen 3 Coder Next   | `qwen/qwen3-coder-next`   | 80B · 4-bit · 44.9 GB     | `0.2`       | Code generation, review, debugging, and implementation guidance    |
| `architecture` | OpenAI GPT-OSS 120B | `openai/gpt-oss-120b`     | 120B · MXFP4 · 63.4 GB    | `0.3`       | System design, planning, trade-offs, and maintainability reviews   |

The model profile is display metadata for the currently installed local
artifact. The provider model identifier is the API contract and must exactly
match an identifier returned by LM Studio's `GET /v1/models`.

### Selection Rules

- A conversation is created with one validated `AiMode`.
- Laravel resolves the model once and stores both the mode and model identifier
  on `ai_conversations`.
- Every later request in that conversation uses the stored model identifier.
- Changing `config/ai.php` affects new conversations only. Existing
  conversations remain pinned to their stored model.
- The application does not inspect a prompt and automatically switch models.
- The local MVP does not expose a manual model-identifier override.

These rules make conversation behavior predictable and keep provider details
out of user input.

## Request Lifecycle

1. An authenticated user sends a message to a conversation they own.
2. Laravel applies the `throttle:ai` rate limit and validates the prompt.
3. The user message is stored immediately. The first prompt also becomes the
   conversation title, truncated to a safe display length.
4. Laravel creates an `ai_requests` row with `processing` status and links it to
   the originating user message.
5. `PromptBuilder` loads the mode-specific system prompt from
   `resources/prompts/ai/`.
6. `AiConversationService` adds a bounded window of Laravel-owned user and
   assistant history.
7. The provider receives the stored model identifier, messages, allowlisted
   tool definitions, mode temperature, and output-token limit.
8. `LmStudioProvider` calls `POST /v1/chat/completions` with streaming enabled
   and parses text, usage, and tool-call fragments.
9. Laravel streams application-level SSE events to the browser.
10. If the model requests an approved tool, Laravel validates and executes it,
    adds the result to provider context, and permits at most one follow-up model
    round with tools disabled.
11. A non-empty assistant response is stored in `ai_messages`. Request status,
    latency, and token counts, when supplied by the provider, are recorded.
12. Laravel sends a final `complete` event containing server-rendered,
    sanitized HTML.

The default context and generation limits are:

- up to 8,000 characters per submitted prompt;
- up to 30 recent user/assistant messages;
- up to 2,048 output tokens;
- 10 AI requests per authenticated user per minute.

All limits are configuration-driven.

## Prompts and Conversation Context

Each mode has a server-side system prompt:

- `resources/prompts/ai/general.md`
- `resources/prompts/ai/coding.md`
- `resources/prompts/ai/architecture.md`

Prompt files are application code. They must be reviewed and versioned with the
feature that depends on them. User input must never be interpolated into a
system prompt template.

Laravel reconstructs context on every request from its own database. Provider
session state is neither required nor trusted. The bounded history window
prevents an unbounded request payload; it is a message-count limit, not a token
budget.

## Streaming and Markdown Rendering

There are two separate streams:

1. LM Studio streams OpenAI-compatible SSE data to Laravel.
2. Laravel emits its own same-origin SSE events to the authenticated browser.

Application events are:

- `delta`: a raw text fragment and, periodically, a complete sanitized
  `rendered_html` snapshot;
- `status`: a user-facing progress message, including approved tool activity;
- `complete`: final message metadata and final sanitized HTML;
- `error`: a safe error message and machine-readable error code.

Laravel accumulates model text and refreshes `rendered_html` approximately every
75 milliseconds or when a newline arrives. This allows headings, emphasis,
lists, and code blocks to render correctly while the response is still being
generated.

`AiMarkdownRenderer` uses Laravel's CommonMark integration with raw HTML
stripped and unsafe links disabled. The browser may keep raw Markdown in memory
while assembling the stream, but it inserts only Laravel-rendered HTML into the
response container. Persisted assistant messages use the same renderer when
the conversation is opened again.

## Tool Calling

Tools are enabled with `AI_TOOLS_ENABLED=true`. `AiToolRegistry` exposes only:

- `list_course_modules`;
- `get_course_module`;
- `get_project_stack`.

Arguments are schema-limited and validated again before execution. Unknown
tools fail closed. The model cannot execute shell commands, read arbitrary
files, issue SQL, or call arbitrary URLs. Tool output is used only as provider
context for the single allowed follow-up round.

## Failure, Stop, and Retry Behavior

Provider connection, HTTP, invalid-stream, invalid-tool, and empty-response
failures are converted to safe application errors. The request telemetry row is
marked `failed`; prompt or response content is not written to operational logs.
Failure of LM Studio does not affect authentication, coursework, or other
cabinet features.

The browser's **Stop** button aborts its fetch request and stops displaying the
stream. It is not a guarantee that local inference has already stopped on the
server.

A failed request is retryable. A `processing` request is also retryable when it
is older than the configured provider timeout plus 30 seconds, with a minimum
stale threshold of 60 seconds. Retry reuses the original stored user message
and creates a new telemetry row; it does not duplicate the user message.

## Persistence and Observability

Conversation content is intentionally stored in `ai_messages`. Operational
telemetry is stored separately in `ai_requests`:

- conversation, user, and originating user-message identifiers;
- mode, provider, and model;
- processing status and error code;
- latency;
- prompt and completion token counts when LM Studio supplies them.

The telemetry table does not duplicate prompt or response text. Application
logs contain request identifiers, error codes, and exception classes, not
conversation content. Successful responses also create a metadata-only
`ai.response.completed` activity event.

## Security and Privacy Invariants

- Every AI route requires an authenticated Laravel session.
- Conversations and messages are resolved through the authenticated user's
  relationship.
- Browser requests use Laravel CSRF protection and a per-user AI rate limit.
- Provider configuration and compatibility credentials remain server-side.
- LM Studio should listen only on `127.0.0.1` for local development.
- Model output is untrusted content and is sanitized before HTML insertion.
- Tools are read-only, allowlisted, and validated by Laravel.
- Conversation text is stored in the application database but excluded from
  operational telemetry and logs.

## Changing a Model Safely

1. Install the model in LM Studio.
2. Confirm its exact API identifier with `GET /v1/models`.
3. Update the relevant mode in `config/ai.php`, including display metadata,
   temperature, and prompt path when required.
4. Run `php artisan config:clear`.
5. Create a new conversation and verify routing, streaming Markdown, tool
   behavior, persistence, and failure handling.
6. Run the AI feature and unit test suites with the fake provider; automated
   tests must not require a live model.

If an old model is being removed, decide explicitly whether to keep it
available for existing conversations or migrate/archive those conversations.
Changing the configuration alone does not rewrite stored model identifiers.

See [Local AI Setup with LM Studio](ai-local-setup.md) for startup and provider
verification commands.
