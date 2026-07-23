# Engineering Documentation

This directory contains architecture, security, operations, and product
requirements documentation for the CS85 Laravel application.

## Authentication and Account Security

Start with the [Authentication and Account Security handbook](authentication/README.md).

- [Architecture and security controls](authentication/architecture.md)
- [Audit event catalog](authentication/audit-events.md)
- [Operations and incident response](authentication/operations.md)
- [Testing strategy and quality gates](authentication/testing.md)

## AI Platform

- [Software requirements specification](AI_PLATFORM_SRS.md)
- [Architecture](architecture/ai-architecture.md)
- [AI model runtime and request lifecycle](architecture/ai-model-runtime.md)
- [Database design](architecture/ai-database.md)
- [Local setup](architecture/ai-local-setup.md)
- [Model routing](architecture/ai-routing.md)

## Architecture Decisions

- [ADR-001: Provider pattern](decisions/ADR-001-provider-pattern.md)
- [ADR-002: Model routing](decisions/ADR-002-model-routing.md)
- [ADR-003: Laravel-owned conversation history](decisions/ADR-003-laravel-owned-conversation-history.md)
- [ADR-004: Streaming and safe tools](decisions/ADR-004-streaming-and-safe-tools.md)

Documentation is part of the implementation contract. Changes to routes,
configuration, persistence, security controls, audit events, deployment, or
support procedures must update the relevant documents in the same pull request.
