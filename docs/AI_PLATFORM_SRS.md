# AI Platform Software Requirements Specification

## 1. Purpose

This document defines the architecture, implementation rules, quality
standards, and engineering requirements for integrating a hybrid local/online
AI platform into the Laravel application.

## 2. Vision

Transform the application into a professional AI-powered learning platform,
not merely a chatbot.

## 3. Goals

- Provider-based architecture
- Multiple AI models
- OpenAI-compatible API
- Explicit local and online privacy boundaries
- Clean Laravel architecture
- Testability

## 4. Supported Models

| Mode          | Provider   | Display name        | Model identifier        |
| ------------- | ---------- | ------------------- | ----------------------- |
| General       | LM Studio  | Qwen 3.6 35B A3B    | `qwen/qwen3.6-35b-a3b`  |
| Coding        | LM Studio  | Qwen 3 Coder Next   | `qwen/qwen3-coder-next` |
| Architecture  | LM Studio  | OpenAI GPT-OSS 120B | `openai/gpt-oss-120b`   |
| OpenAI Online | OpenAI API | OpenAI GPT-4o mini  | `gpt-4o-mini`           |

Users select a mode when creating a conversation. Laravel resolves and stores
the configured provider and model identifiers, which remain fixed for that
conversation. The browser cannot submit an arbitrary provider or model.

## 5. High-Level Architecture

Browser
→ Controller
→ FormRequest
→ AI Service
→ AI Provider Interface
→ Routed Provider
→ LM Studio or OpenAI API
→ Selected Model

## 6. Functional Requirements

The platform shall:

- answer programming questions
- explain concepts
- review code
- generate quizzes
- generate code
- debug exceptions
- maintain conversation history
- support provider switching
- route new conversations from a validated mode to a configured model
- stream responses to the authenticated browser
- isolate conversation history by Laravel user
- expose only allowlisted read-only application tools
- report server-side connection state for all four configured models

## 6.1 Phase 1 Product Decisions

- Both standard users and administrators may use the AI workspace.
- The application supports persistent multi-turn conversations.
- Laravel owns conversation history; provider-side state is not required.
- LM Studio serves three local modes, and the OpenAI API serves the explicit
  `online` mode through the same application-facing provider contract.
- The OpenAI-compatible `POST /v1/chat/completions` endpoint is used with streaming enabled.
- Provider base URLs are `http://127.0.0.1:1234/v1` for LM Studio and
  `https://api.openai.com/v1` for OpenAI.
- Laravel renders model Markdown during streaming and strips raw HTML and unsafe links.
- Tools are limited to read-only course configuration lookups.
- The complete runtime contract is documented in
  [AI Model Runtime](architecture/ai-model-runtime.md).

## 7. Non-functional Requirements

- SOLID
- PSR-12
- Laravel 13 conventions
- Dependency Injection
- Service Layer
- DTOs
- Feature Tests
- Security-first

## 8. Provider Pattern

Business logic must never call HTTP directly.

Every provider implements AiProviderInterface.

Providers:

- LmStudioProvider
- OpenAiProvider
- RoutedAiProvider

## 9. Model Registry

Models are configured through config/ai.php.

No hardcoded model names.

## 10. Security

- .env only
- Rate limiting
- XSS protection
- CSRF
- Server-rendered and sanitized AI Markdown
- No secrets in Git
- Per-user conversation ownership
- No shell, filesystem, arbitrary URL, or SQL tools
- No prompt or response content in operational logs
- Clear disclosure when online-mode context is sent to OpenAI

## 11. Testing

- Feature tests
- Unit tests
- Mock providers
- No real AI requests
- Streaming event parsing tests
- Conversation ownership tests
- Tool allowlist tests
- Provider routing and model-catalog health tests

## 12. Acceptance Criteria

- Existing features continue working.
- AI module is isolated.
- Providers are swappable.
- New models require configuration only.
- The application remains usable when either provider is offline.
- The cabinet reports three local model states and one online model state
  without exposing credentials.
