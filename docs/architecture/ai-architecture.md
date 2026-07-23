# AI Architecture

## System Boundary

```text
Authenticated browser
    -> Laravel controller and form request
        -> AI conversation service
            -> AI provider interface
                -> LM Studio
                    -> conversation's configured model
```

Laravel owns authentication, authorization, history, prompts, model routing,
tools, Markdown rendering, persistence, and telemetry. LM Studio is an
OpenAI-compatible local inference provider.

## Runtime Flow

1. An authenticated user creates a conversation in one configured mode.
2. Laravel resolves the mode to a model and stores that model on the
   conversation.
3. Laravel stores the user message and creates a linked processing telemetry
   record.
4. The AI service loads a bounded history window and the mode-specific system
   prompt.
5. The provider streams `/v1/chat/completions` events from LM Studio.
6. Laravel periodically renders accumulated Markdown into sanitized HTML and
   forwards application-level SSE events to the browser.
7. If the model requests an allowlisted read-only tool, Laravel validates and
   executes it, then permits one final provider round with tools disabled.
8. The final assistant message and metadata-only request telemetry are
   persisted.

The detailed contract, including streaming, retries, tools, security, and
model-change procedures, is defined in
[AI Model Runtime](ai-model-runtime.md).

## Local Runtime

The local AI workspace requires LM Studio to listen on `127.0.0.1:1234` and
expose the configured model identifiers through its OpenAI-compatible API. See
[Local AI Setup with LM Studio](ai-local-setup.md) for the complete startup,
verification, and troubleshooting procedure.

## Routing Strategy

- General learning and programming questions use `qwen/qwen3.6-35b-a3b`.
- Code generation, review, and debugging use `qwen/qwen3-coder-next`.
- Architecture and technical planning use `openai/gpt-oss-120b`.

Users choose the mode explicitly. The application does not classify prompts or
change models during a conversation. See [AI Routing](ai-routing.md).

## Failure Boundary

LM Studio availability is optional for the rest of the application. Provider
failures produce a safe streamed error and do not affect authentication,
coursework, admin, or other cabinet features.
