# AI Database

The local MVP uses three related tables.

## ai_conversations

- id
- public_uuid
- user_id
- title
- mode
- model
- timestamps

## ai_messages

- id
- ai_conversation_id
- role
- content
- metadata
- timestamps

## ai_requests

Operational telemetry only:

- id
- ai_conversation_id
- user_message_id
- user_id
- mode
- provider
- model
- prompt_tokens
- completion_tokens
- latency_ms
- status
- error_code
- timestamps

`user_message_id` links every provider attempt to the user message that caused
it. A retry creates another request record for the same user message instead of
duplicating conversation content.

Prompts and responses are not duplicated in request telemetry. Conversation
ownership is enforced through `user_id`, and deleting a conversation cascades
to its messages and request records.
