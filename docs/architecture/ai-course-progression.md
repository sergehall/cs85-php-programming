# Module 12 AI Course Progression

Module 12 contains two intentionally separate AI deliverables. They share a
server-side service boundary but solve different assessment problems.

## Assignment 12A

The course-aligned implementation at `/ai-form` demonstrates the required
OpenAI fundamentals without hiding them inside the larger application:

- OpenAI Chat Completions with `gpt-4o-mini`;
- `.env` and `config/services.php` configuration;
- title, content type, and tone validation;
- a dedicated `AiContentService`;
- role and task prompt construction;
- failed-response logging and safe user feedback;
- editable generated output;
- controller service substitution and Laravel HTTP fakes.

The source and setup guide live in
[`assignments/module12a`](../../assignments/module12a).

## Final Project

The authenticated workspace at `/cabinet/ai` expands the same design rule -
providers stay behind Laravel - into a complete AI-powered web application:

- an application-facing provider interface;
- explicit conversation-scoped model routing;
- local OpenAI-compatible inference through LM Studio;
- database-backed multi-turn history;
- same-origin SSE streaming;
- safe Markdown rendering;
- allowlisted read-only tools;
- request telemetry;
- rate limiting, timeouts, failure isolation, and retry.

The final project guide lives in
[`assignments/final-project-ai`](../../assignments/final-project-ai).

## Why the Implementations Stay Separate

Using the final project as the only submission would make the basic assignment
requirements harder to grade. Using the basic assignment as the final project
would underrepresent the completed architecture.

The dual-track structure solves both problems:

| Assessment goal          | Evidence                                                   |
| ------------------------ | ---------------------------------------------------------- |
| Understand a direct API  | `AiContentService` and `/ai-form`                          |
| Adapt prompts            | Type and tone `match` expressions                          |
| Protect an API key       | Server-side OpenAI configuration                           |
| Handle failures          | HTTP checks, safe errors, logging, and fake-backed tests   |
| Build a complete product | `/cabinet/ai` conversations, streaming, tools, and history |
| Demonstrate architecture | Provider contract and versioned documents                  |
| Demonstrate operations   | Rate limits, telemetry, retry, and local runtime setup     |

This preserves the learning progression from one controlled external request
to a maintainable AI system.
