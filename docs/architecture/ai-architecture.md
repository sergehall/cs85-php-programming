# AI Architecture

For the relationship between the course-aligned OpenAI generator and this
final-project architecture, see
[Module 12 AI Course Progression](ai-course-progression.md).

## System Boundary

```text
Authenticated browser
    -> Laravel controller and form request
        -> AI conversation service
            -> AI provider interface
                -> routed provider
                    -> LM Studio -> three local models
                    -> OpenAI API -> gpt-4o-mini
```

Laravel owns authentication, authorization, history, prompts, provider/model
routing, tools, Markdown rendering, persistence, telemetry, and safe failure
behavior. LM Studio and OpenAI perform inference only. The browser never calls
either provider directly and never receives a provider credential.

## Runtime Flow

1. An authenticated user creates a conversation in one configured mode.
2. Laravel resolves and stores both the provider and model on the conversation.
3. Laravel stores the user message and creates a linked processing telemetry
   record.
4. The AI service loads a bounded history window and the mode-specific system
   prompt.
5. `RoutedAiProvider` selects LM Studio or OpenAI from the stored provider.
6. The selected provider streams OpenAI-compatible Chat Completions events.
7. Laravel periodically renders accumulated Markdown into sanitized HTML and
   forwards application-level SSE events to the browser.
8. If the model requests an allowlisted read-only tool, Laravel validates and
   executes it, then permits one final provider round with tools disabled.
9. The final assistant message and metadata-only request telemetry are
   persisted.

The detailed contract, including streaming, retries, tools, security, and
model-change procedures, is defined in
[AI Model Runtime](ai-model-runtime.md).

## Provider and Model Health

The authenticated `GET /cabinet/ai/status` endpoint performs a server-side
provider check:

- `GET http://127.0.0.1:1234/v1/models` validates LM Studio and the three local
  model identifiers;
- `GET https://api.openai.com/v1/models` validates the OpenAI key and
  `gpt-4o-mini`;
- the browser receives only connection state, model metadata, safe messages,
  and latency;
- neither the LM Studio compatibility key nor `OPENAI_API_KEY` is returned.

The connection monitor is operational evidence. Automated tests replace both
HTTP boundaries with `Http::fake()`.

## Routing Strategy

| Mode          | Provider   | Model                   |
| ------------- | ---------- | ----------------------- |
| General       | LM Studio  | `qwen/qwen3.6-35b-a3b`  |
| Coding        | LM Studio  | `qwen/qwen3-coder-next` |
| Architecture  | LM Studio  | `openai/gpt-oss-120b`   |
| OpenAI Online | OpenAI API | `gpt-4o-mini`           |

Users choose the mode explicitly. The application does not classify prompts or
change providers during a conversation. See [AI Routing](ai-routing.md).

## Failure Boundary

Each provider is optional for the rest of the application. A local outage does
not prevent use of the online mode, and an OpenAI outage does not prevent local
inference. Provider failures produce a safe streamed error and do not affect
authentication, coursework, admin, or other cabinet features.
