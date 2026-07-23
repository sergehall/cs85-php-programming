# ADR-004

Decision:
Use the OpenAI-compatible streaming chat completions endpoint, render
sanitized Markdown snapshots in Laravel during streaming, and expose only
allowlisted read-only course tools.

Reason:
Streaming improves local-model UX while server-side Markdown rendering keeps
formatting consistent for live and persisted responses. Raw HTML and unsafe
links are rejected. A narrow tool allowlist supports grounded course answers
without granting the model shell, filesystem, SQL, or arbitrary network
access.

Consequences:

- The browser inserts only Laravel-rendered response HTML.
- A model may trigger at most one follow-up provider round after approved tool
  execution.
- Unknown tools and invalid arguments fail closed.

Status:
Accepted.
