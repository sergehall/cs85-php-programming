# AI Architecture

## Layers

UI
↓
Controller
↓
Form Request
↓
AI Service
↓
Provider Interface
↓
LM Studio
↓
Model

## Runtime Flow

1. An authenticated user creates a conversation in one configured mode.
2. Laravel stores user and assistant messages in the application database.
3. The AI service loads a bounded history window and a versioned system prompt.
4. The provider streams `/v1/chat/completions` events from LM Studio.
5. Laravel forwards escaped text deltas to the browser over a same-origin SSE response.
6. If the model requests an allowlisted read-only tool, Laravel validates and executes it, then performs one final provider round.
7. The final assistant message and metadata-only request telemetry are persisted.

Laravel owns history and authorization. LM Studio is an inference provider and may be replaced through the provider contract.

## Local Runtime

The local AI workspace requires LM Studio to listen on `127.0.0.1:1234` and
expose the configured model identifiers through its OpenAI-compatible API. See
[Local AI Setup with LM Studio](ai-local-setup.md) for the complete startup,
verification, and troubleshooting procedure.

## Supported Models

General:

- qwen/qwen3.6-35b-a3b

Coding:

- qwen/qwen3-coder-next

Architecture:

- openai/gpt-oss-120b

Routing Strategy

General questions → General model

Code generation/review → Coding model

Architecture & planning → GPT-OSS

## Failure Boundary

LM Studio availability is optional for the rest of the application. Provider failures produce a safe streamed error and do not affect authentication, coursework, admin, or other cabinet features.
