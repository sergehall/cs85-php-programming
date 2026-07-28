# Hybrid AI Setup: LM Studio and OpenAI

This guide describes the required runtime state for the Laravel AI assistant.
The application uses LM Studio for three local models and the OpenAI API for
one online model. The browser never connects to either provider directly; it
sends authenticated requests to Laravel, and Laravel selects the provider
stored on the conversation.

For the application-level model selection, prompt, streaming, tool, retry, and
security contract, see [AI Model Runtime](ai-model-runtime.md).

## Required Runtime State

The AI workspace works only when all of the following are true:

- The LM Studio application or background service is running.
- The LM Studio local API server is enabled on `127.0.0.1:1234`.
- The required models are installed and visible through `GET /v1/models`.
- The model identifier returned by LM Studio matches the identifier in
  `config/ai.php`.
- Laravel has a valid `.env`, completed database migrations, and a cleared
  configuration cache after environment changes.
- Laravel and the frontend assets are running.
- The user is authenticated before opening `/cabinet/ai`.
- `OPENAI_API_KEY` is configured for the online mode.
- The OpenAI project can access the configured `OPENAI_MODEL`.

Provider CORS does not need to be enabled. All calls are server-side, and the
LM Studio API should remain bound to `127.0.0.1`.

## Configured Models

| AI mode       | Required API model identifier |
| ------------- | ----------------------------- |
| General       | `qwen/qwen3.6-35b-a3b`        |
| Coding        | `qwen/qwen3-coder-next`       |
| Architecture  | `openai/gpt-oss-120b`         |
| OpenAI Online | `gpt-4o-mini` via OpenAI API  |

The API identifiers are the application contract. Do not put model file paths
in Laravel configuration.

The selected identifier is stored when a conversation is created. After
changing a mapping, create a new conversation to verify the new model; existing
conversations remain pinned to their stored identifier.

The current workstation stores the models under these locations:

```text
/Users/sergehall/.lmstudio/hub/models/qwen/qwen3-coder-next
/Users/sergehall/.lmstudio/models/lmstudio-community/gpt-oss-120b-GGUF/gpt-oss-120b-MXFP4-00001-of-00002.gguf
/Users/sergehall/.lmstudio/hub/models/qwen/qwen3.6-35b-a3b
```

These paths are informational and may differ on another machine. Always use
`GET /v1/models` to discover the model identifiers accepted by the API.

## One-Time Laravel Configuration

Copy the example environment file and prepare the application if this has not
already been done:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
```

The local `.env` must contain the LM Studio and OpenAI provider settings:

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

`AI_LM_STUDIO_API_KEY` is a compatibility value for the local endpoint.
`OPENAI_API_KEY` is the real cloud credential and must never be committed,
rendered in Blade, returned from a controller, or placed in screenshots.

After changing `.env` or `config/ai.php`, clear Laravel's configuration cache:

```bash
php artisan config:clear
```

Confirm the effective configuration:

```bash
php artisan config:show ai
```

## Start LM Studio

Open LM Studio and keep it running. In the **Developer** area, start the local
server on port `1234` and bind it to `127.0.0.1`.

The same operation can be performed with the bundled macOS CLI:

```bash
~/.lmstudio/bin/lms server start --port 1234 --bind 127.0.0.1
```

If `lms` has been installed into the shell `PATH`, the shorter command is:

```bash
lms server start --port 1234 --bind 127.0.0.1
```

Check the server status:

```bash
~/.lmstudio/bin/lms server status
```

Expected result:

```text
The server is running on port 1234.
```

## Verify Installed Models

List models available through the OpenAI-compatible API:

```bash
curl http://127.0.0.1:1234/v1/models
```

The response must include the three configured identifiers:

```text
qwen/qwen3.6-35b-a3b
qwen/qwen3-coder-next
openai/gpt-oss-120b
```

Verify the OpenAI project and model from the server without exposing the key:

```bash
php artisan config:show services.openai
```

The command confirms the effective URL and model configuration. Do not paste
its output into a submission if the local framework version displays secrets.
The cabinet performs the safer operational check through
`GET /cabinet/ai/status`; that response never contains the key.

LM Studio can load the selected model automatically when Laravel sends the
first request. To avoid the initial loading delay, load a model in the LM Studio
UI before opening the chat. The General model can also be loaded through the
CLI:

```bash
~/.lmstudio/bin/lms load qwen/qwen3.6-35b-a3b -y
```

Show currently loaded models:

```bash
~/.lmstudio/bin/lms ps
```

Model loading can require significant memory and may take several seconds. Do
not load all large models simultaneously unless the machine has enough memory.

## Verify Streaming Inference

Run a small provider-level streaming test before troubleshooting Laravel:

```bash
curl -N http://127.0.0.1:1234/v1/chat/completions \
  -H 'Content-Type: application/json' \
  -d '{
    "model": "qwen/qwen3.6-35b-a3b",
    "messages": [
      {
        "role": "user",
        "content": "Reply briefly: LM Studio connected."
      }
    ],
    "max_tokens": 512,
    "stream": true
  }'
```

A working server returns multiple lines beginning with `data:` and finishes
with `data: [DONE]`.

## Start the Laravel Application

Start the complete local development stack:

```bash
npm run dev
```

Alternatively, run Laravel and Vite separately:

```bash
php artisan serve --host=127.0.0.1 --port=8000
npm run dev:assets
```

Then:

1. Open `http://127.0.0.1:8000`.
2. Register or sign in.
3. Open `http://127.0.0.1:8000/cabinet/ai`.
4. Confirm the connection panel reports the expected local and online states.
5. Select General, Coding, Architecture, or OpenAI Online mode.
6. Create a conversation and send a message.

The first response can be slower because LM Studio may need to load the model.
Later requests use the already loaded model until its configured TTL expires.

## Recommended Startup Order

Use this order after restarting the computer:

1. Start LM Studio.
2. Start the LM Studio server on `127.0.0.1:1234`.
3. Confirm `GET /v1/models` returns the three local model identifiers.
4. Confirm `OPENAI_API_KEY` and `OPENAI_MODEL=gpt-4o-mini` are in `.env`.
5. Optionally pre-load the local model needed for the selected AI mode.
6. Start the Laravel development stack.
7. Sign in and open `/cabinet/ai`.
8. Wait for the four connection cards, then use **Check again** if needed.

## Troubleshooting

### `LM Studio is unavailable`

Cause: Laravel cannot connect to `127.0.0.1:1234`.

Checks:

```bash
~/.lmstudio/bin/lms server status
curl http://127.0.0.1:1234/v1/models
php artisan config:show ai
```

Start the server and clear stale Laravel configuration:

```bash
~/.lmstudio/bin/lms server start --port 1234 --bind 127.0.0.1
php artisan config:clear
```

### `LM Studio rejected the request`

Cause: the configured model identifier is missing, the model failed to load,
or LM Studio returned an HTTP error.

Verify that `/v1/models` contains the exact identifier from `config/ai.php`.
Then load the model in LM Studio and retry.

### The first response is slow

This is expected when a large model is loaded from disk. Pre-load the selected
model and check its state with `~/.lmstudio/bin/lms ps`.

### The response stops early or is empty

Some reasoning models use part of the output token limit for internal reasoning.
Keep `AI_MAX_OUTPUT_TOKENS=2048` for normal application use and avoid reducing
the limit to very small values during manual testing.

### A mode uses the wrong model

Check the mode mapping in `config/ai.php`, then run:

```bash
php artisan config:clear
php artisan config:show ai
```

### The AI page opens but sending fails

Confirm that the user is authenticated, database migrations are current, and
the frontend assets are running:

```bash
php artisan migrate:status
npm run build
```

### `OpenAI API key is not configured`

Add `OPENAI_API_KEY` to `.env`, then clear cached configuration:

```bash
php artisan config:clear
```

Never add the real key to `.env.example` or Git.

### OpenAI health check is rejected

Confirm the key is active, the OpenAI project is funded for API use, and the
project can access `gpt-4o-mini`. ChatGPT subscriptions and OpenAI API billing
are separate.

## Security Notes

- Keep the LM Studio server bound to `127.0.0.1`.
- Keep the OpenAI key only in `.env` or a production secret manager.
- Online-mode prompt context leaves the local machine and is sent to OpenAI.
- Do not expose port `1234` to the public internet.
- Do not enable CORS for this server-side Laravel integration.
- Do not commit `.env` or real provider secrets.
- Do not replace API model identifiers with local filesystem paths.
- AI failure must not block authentication, coursework, or other cabinet areas.
