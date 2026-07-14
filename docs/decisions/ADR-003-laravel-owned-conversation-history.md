# ADR-003

Decision:
Laravel owns persistent multi-turn conversation history.

Reason:
Application-owned history provides deterministic authorization, deletion, testing, and provider switching without depending on LM Studio server state.

Status:
Accepted.
