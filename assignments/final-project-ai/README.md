# Final Project: AI-Powered Web Application

The Final Project is the authenticated Laravel AI workspace at `/cabinet/ai`.
It extends the single-request OpenAI exercise from Assignment 12A into a
production-oriented, local-first AI application.

## Product Goal

The workspace helps authenticated students learn programming, review code, and
reason about architecture while keeping conversation history and model
execution under local application control.

## Implemented Capabilities

- authenticated and verified-user access;
- conversation ownership enforced through the current user relationship;
- three explicit task modes with server-controlled model routing;
- provider abstraction through `AiProviderInterface`;
- LM Studio integration over an OpenAI-compatible Chat Completions endpoint;
- streamed Server-Sent Events from Laravel to the browser;
- bounded, database-backed multi-turn history;
- versioned system prompts;
- allowlisted, schema-validated, read-only course tools;
- server-rendered and sanitized Markdown;
- provider timeouts and typed safe failures;
- retry without duplicating the original user message;
- request latency, status, model, provider, and token telemetry;
- per-user rate limiting;
- automated feature, unit, provider, and frontend behavior tests.

## Model Routing

| Mode         | Model identifier        | Primary use                             |
| ------------ | ----------------------- | --------------------------------------- |
| General      | `qwen/qwen3.6-35b-a3b`  | Learning, explanations, quizzes         |
| Coding       | `qwen/qwen3-coder-next` | Code generation, review, and debugging  |
| Architecture | `openai/gpt-oss-120b`   | Planning, trade-offs, and system design |

The selected model is stored on the conversation. Configuration changes affect
new conversations only, which keeps existing history predictable.

## Architecture

```text
Authenticated browser
    -> Laravel controller and form request
        -> AI conversation service
            -> AI provider interface
                -> LM Studio OpenAI-compatible API
                    -> conversation's configured local model
```

Laravel owns authentication, authorization, prompts, history, tools, Markdown
rendering, persistence, telemetry, and application-level streaming. LM Studio
owns only local inference.

## Documentation

- [`docs/architecture/ai-architecture.md`](../../docs/architecture/ai-architecture.md)
- [`docs/architecture/ai-model-runtime.md`](../../docs/architecture/ai-model-runtime.md)
- [`docs/architecture/ai-routing.md`](../../docs/architecture/ai-routing.md)
- [`docs/architecture/ai-database.md`](../../docs/architecture/ai-database.md)
- [`docs/architecture/ai-local-setup.md`](../../docs/architecture/ai-local-setup.md)
- [`docs/architecture/ai-course-progression.md`](../../docs/architecture/ai-course-progression.md)

## Local Setup

Follow the complete
[`ai-local-setup.md`](../../docs/architecture/ai-local-setup.md) guide. The
required local provider settings are:

```dotenv
AI_PROVIDER=lm_studio
AI_LM_STUDIO_BASE_URL=http://127.0.0.1:1234/v1
AI_LM_STUDIO_API_KEY=lm-studio
AI_CONNECT_TIMEOUT=5
AI_REQUEST_TIMEOUT=180
AI_PROMPT_MAX_CHARACTERS=8000
AI_HISTORY_MESSAGES=30
AI_MAX_OUTPUT_TOKENS=2048
AI_REQUESTS_PER_MINUTE=10
AI_TOOLS_ENABLED=true
```

The compatibility key is not an OpenAI cloud key. LM Studio should remain
bound to `127.0.0.1` during local development.

## Verification

```bash
php artisan test --filter=Ai
node --test tests/node/ai-chat.test.mjs
composer run format:check
composer run lint
npm run build
```

Automated tests use a fake provider or fake HTTP responses and do not require a
running model.

## Assignment Relationship

Assignment 12A is deliberately preserved as a small OpenAI `gpt-4o-mini`
content generator. It exposes the core mechanics the rubric asks the grader to
find. The Final Project reuses the same central rule - AI calls stay behind a
server-side service boundary - and expands everything around that boundary for
security, privacy, reliability, observability, and user experience.

## GitHub

- [Final Project source](https://github.com/SergeHall/cs85-php-programming/tree/main/app/Services/AI)
- [Final Project documentation](https://github.com/SergeHall/cs85-php-programming/tree/main/docs/architecture)
- [Module 12 coursework](https://github.com/SergeHall/cs85-php-programming/tree/main/assignments/module12a)
