# ADR-002

Decision:
Route each new conversation from an explicit user-selected mode to a model
configured in `config/ai.php`. Persist the resolved model identifier on the
conversation and use it for every later turn.

Reason:
Task-oriented modes provide predictable specialization without accepting
provider model identifiers from the browser. Persisting the selection keeps a
conversation stable when configuration changes.

Consequences:

- Existing conversations remain pinned to their stored model.
- The local MVP has no automatic prompt classifier or manual model override.
- Retiring a model requires an explicit plan for conversations that reference
  it.

Status:
Accepted.
