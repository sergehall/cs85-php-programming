# AI Platform Software Requirements Specification

## 1. Purpose

This document defines the architecture, implementation rules, quality standards,
and engineering requirements for integrating a local AI Platform into the Laravel application.

## 2. Vision

Transform the application into a professional AI-powered learning platform,
not merely a chatbot.

## 3. Goals

- Provider-based architecture
- Multiple AI models
- OpenAI-compatible API
- Local-first design
- Clean Laravel architecture
- Testability

## 4. Supported Models

| Role         | Model                 |
| ------------ | --------------------- |
| General      | qwen/qwen3.6-35b-a3b  |
| Coding       | qwen/qwen3-coder-next |
| Architecture | openai/gpt-oss-120b   |

## 5. High-Level Architecture

Browser
→ Controller
→ FormRequest
→ AI Service
→ AI Provider Interface
→ LM Studio
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
- support model switching
- stream responses to the authenticated browser
- isolate conversation history by Laravel user
- expose only allowlisted read-only application tools

## 6.1 Phase 1 Product Decisions

- Both standard users and administrators may use the AI workspace.
- The application supports persistent multi-turn conversations.
- Laravel owns conversation history; provider-side state is not required.
- LM Studio is the only active provider for the local MVP.
- The OpenAI-compatible `POST /v1/chat/completions` endpoint is used with streaming enabled.
- The default base URL is `http://127.0.0.1:1234/v1`.
- Provider output is rendered as escaped plain text.
- Tools are limited to read-only course configuration lookups.

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
- OpenAiProvider (future)

## 9. Model Registry

Models are configured through config/ai.php.

No hardcoded model names.

## 10. Security

- .env only
- Rate limiting
- XSS protection
- CSRF
- Escaped AI output
- No secrets in Git
- Per-user conversation ownership
- No shell, filesystem, arbitrary URL, or SQL tools
- No prompt or response content in operational logs

## 11. Testing

- Feature tests
- Unit tests
- Mock providers
- No real AI requests
- Streaming event parsing tests
- Conversation ownership tests
- Tool allowlist tests

## 12. Acceptance Criteria

- Existing features continue working.
- AI module is isolated.
- Providers are swappable.
- New models require configuration only.
- The application remains usable when LM Studio is offline.
