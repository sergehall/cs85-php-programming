# AI Routing

Provider and model routing is explicit and conversation-scoped. Users select a
mode when creating a conversation, and `ModelRouter` resolves the provider and
model from `config/ai.php`.

| Mode           | Provider    | Model identifier        | Temperature |
| -------------- | ----------- | ----------------------- | ----------- |
| `general`      | `lm_studio` | `qwen/qwen3.6-35b-a3b`  | `0.4`       |
| `coding`       | `lm_studio` | `qwen/qwen3-coder-next` | `0.2`       |
| `architecture` | `lm_studio` | `openai/gpt-oss-120b`   | `0.3`       |
| `online`       | `openai`    | `gpt-4o-mini`           | `0.4`       |

Both identifiers are stored on `ai_conversations` and used for every request
in that conversation. A configuration change affects new conversations only.

The application has no prompt classifier, automatic cross-provider fallback,
or user-entered provider/model override. The UI exposes allowlisted modes and
display metadata; transport identifiers remain server-controlled. Explicit
routing makes privacy behavior predictable: local modes stay behind Laravel
and LM Studio, while the online mode clearly discloses that prompt context is
sent to OpenAI.

## Provider Transport

`RoutedAiProvider` implements the application-facing `AiProviderInterface` and
delegates by the stored provider:

| Provider   | Base URL                    | Endpoint                 | Protocol                    |
| ---------- | --------------------------- | ------------------------ | --------------------------- |
| LM Studio  | `http://127.0.0.1:1234/v1`  | `POST /chat/completions` | OpenAI-compatible streaming |
| OpenAI API | `https://api.openai.com/v1` | `POST /chat/completions` | OpenAI streaming            |

Both implementations share the OpenAI-compatible stream parser. Provider
credentials are attached only inside Laravel.

## Health Routing

`AiProviderStatusService` groups the four configured modes by provider and
checks each provider model catalog once. The resulting JSON is keyed by mode,
so the browser can update each model card without learning any credential.

See [AI Model Runtime](ai-model-runtime.md) for the full selection, status,
request, and persistence lifecycle.
