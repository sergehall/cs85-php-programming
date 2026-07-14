# AI Routing

Mode -> Model

General -> qwen3.6

Coding -> qwen3-coder-next

Architecture -> gpt-oss-120b

Manual override may be enabled from UI.

The local MVP does not expose manual model override. Users select a mode, and the model is resolved from `config/ai.php`.

Transport:

- Base URL: `http://127.0.0.1:1234/v1`
- Endpoint: `POST /chat/completions`
- Streaming: enabled
- History: supplied by Laravel from the local database
