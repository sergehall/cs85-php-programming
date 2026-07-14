# ADR-004

Decision:
Use the OpenAI-compatible streaming chat completions endpoint and expose only allowlisted read-only course tools.

Reason:
Streaming improves local-model UX. A narrow tool allowlist demonstrates function calling without granting the model shell, filesystem, SQL, or arbitrary network access.

Status:
Accepted.
