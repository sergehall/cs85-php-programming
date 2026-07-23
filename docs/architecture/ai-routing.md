# AI Routing

Model routing is explicit and conversation-scoped. Users select a mode when
creating a conversation, and `ModelRouter` resolves that mode from
`config/ai.php`.

| Mode           | Model identifier        | Temperature |
| -------------- | ----------------------- | ----------- |
| `general`      | `qwen/qwen3.6-35b-a3b`  | `0.4`       |
| `coding`       | `qwen/qwen3-coder-next` | `0.2`       |
| `architecture` | `openai/gpt-oss-120b`   | `0.3`       |

The resolved model identifier is stored on `ai_conversations` and used for
every request in that conversation. A configuration change affects new
conversations only.

The local MVP has no prompt classifier, automatic fallback to another model,
or manual model-identifier override. The UI exposes modes and their configured
display metadata; provider identifiers remain server-controlled.

## Provider Transport

- Provider: `lm_studio`
- Default base URL: `http://127.0.0.1:1234/v1`
- Endpoint: `POST /chat/completions`
- Protocol: OpenAI-compatible streaming chat completions
- History: bounded and supplied by Laravel from its database

See [AI Model Runtime](ai-model-runtime.md) for the full selection and request
lifecycle contract.
