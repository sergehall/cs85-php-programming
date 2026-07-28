# Final Project: AI-Powered Web Application

The Final Project is the authenticated Laravel AI workspace at `/cabinet/ai`.
It extends the single-request OpenAI exercise from Assignment 12A into a
production-oriented hybrid AI application.

## Product Goal

The workspace helps authenticated students learn programming, review code, and
reason about architecture while Laravel owns authorization, conversation
history, provider selection, and operational controls. Students can choose
three local models or the connected OpenAI online model from one interface.

## Implemented Capabilities

- authenticated and verified-user access;
- conversation ownership enforced through the current user relationship;
- four explicit task modes with server-controlled provider and model routing;
- provider abstraction through `AiProviderInterface`;
- LM Studio integration over an OpenAI-compatible Chat Completions endpoint;
- OpenAI `gpt-4o-mini` integration reusing Assignment 12A's server-side
  credentials and API configuration;
- live provider/model monitoring for three local models and one online model;
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

| Mode          | Provider   | Model identifier        | Primary use                             |
| ------------- | ---------- | ----------------------- | --------------------------------------- |
| General       | LM Studio  | `qwen/qwen3.6-35b-a3b`  | Learning, explanations, quizzes         |
| Coding        | LM Studio  | `qwen/qwen3-coder-next` | Code generation, review, and debugging  |
| Architecture  | LM Studio  | `openai/gpt-oss-120b`   | Planning, trade-offs, and system design |
| OpenAI Online | OpenAI API | `gpt-4o-mini`           | Online answers, drafts, and summaries   |

The selected provider and model are stored on the conversation. Configuration
changes affect new conversations only, which keeps existing history
predictable.

## Architecture

```text
Authenticated browser
    -> Laravel controller and form request
        -> AI conversation service
            -> AI provider interface
                -> routed provider
                    -> LM Studio -> one of three local models
                    -> OpenAI API -> gpt-4o-mini
```

Laravel owns authentication, authorization, prompts, history, tools, Markdown
rendering, persistence, telemetry, connection checks, and application-level
streaming. LM Studio and OpenAI own only inference.

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
required local and online provider settings are:

```dotenv
AI_LM_STUDIO_BASE_URL=http://127.0.0.1:1234/v1
AI_LM_STUDIO_API_KEY=lm-studio
AI_CONNECT_TIMEOUT=5
AI_REQUEST_TIMEOUT=180
AI_PROMPT_MAX_CHARACTERS=8000
AI_HISTORY_MESSAGES=30
AI_MAX_OUTPUT_TOKENS=2048
AI_REQUESTS_PER_MINUTE=10
AI_TOOLS_ENABLED=true

OPENAI_API_KEY=your_openai_api_key_here
OPENAI_API_URL=https://api.openai.com/v1
OPENAI_MODEL=gpt-4o-mini
OPENAI_CONNECT_TIMEOUT=5
OPENAI_REQUEST_TIMEOUT=30
```

The LM Studio compatibility key is not an OpenAI cloud key. LM Studio should
remain bound to `127.0.0.1` during local development. The real OpenAI key stays
only in `.env`.

Opening `/cabinet/ai` automatically calls the authenticated
`/cabinet/ai/status` endpoint. Laravel checks both provider model catalogs and
reports each configured model as connected, missing, not configured, rejected,
or unreachable without returning either credential.

## Verification

```bash
php artisan test --filter=Ai
npm run test:node
composer run format:check
composer run lint
npm run build
```

Automated tests use a fake provider or fake HTTP responses and do not require a
running model or spend OpenAI API credits.

## Assignment Relationship

Assignment 12A is deliberately preserved as a small OpenAI `gpt-4o-mini`
content generator. It exposes the core mechanics the rubric asks the grader to
find. The Final Project reuses the same live OpenAI connection as its fourth
routed model and expands the server-side service boundary for security,
privacy, reliability, observability, provider switching, and user experience.

## Final Project Requirements Evidence

| Requirement                  | Implementation evidence                                                          |
| ---------------------------- | -------------------------------------------------------------------------------- |
| AI-powered Laravel interface | Authenticated Blade workspace at `/cabinet/ai`                                   |
| External AI API              | Online mode streams server-side requests to OpenAI `gpt-4o-mini`                 |
| Effective prompts            | Four versioned role prompts in `resources/prompts/ai/`                           |
| Clean architecture           | Controller -> conversation service -> routed provider contract                   |
| Secure secret handling       | `OPENAI_API_KEY` is read from `.env` and never returned to JavaScript            |
| Error handling               | Typed provider errors, safe SSE errors, request telemetry, and retry             |
| Persistence                  | Conversations, messages, provider/model choice, and metadata-only request logs   |
| Testing                      | HTTP fakes, fake provider, routing tests, authorization tests, and Node UI tests |
| Working demonstration        | Live status monitor shows three local models plus one online model               |

## Suggested Submission Screenshots

1. `/cabinet/ai` with all four connection cards visible.
2. The four model/mode selection cards.
3. A successful OpenAI Online conversation with the provider badge visible.
4. `config/ai.php` showing three `lm_studio` modes and one `openai` mode.
5. `RoutedAiProvider`, `OpenAiProvider`, and `AiConversationService` in VS Code.
6. A passing `php artisan test --filter=Ai` result.

## GitHub

- [Final Project source](https://github.com/SergeHall/cs85-php-programming/tree/main/app/Services/AI)
- [Final Project documentation](https://github.com/SergeHall/cs85-php-programming/tree/main/docs/architecture)
- [Module 12 coursework](https://github.com/SergeHall/cs85-php-programming/tree/main/assignments/module12a)
