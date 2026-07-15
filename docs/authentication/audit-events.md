# Authentication Audit Events

## Audit Model

Authentication and account-security events are written to two destinations:

1. `activity_logs`, for user/admin timelines and database investigation;
2. the daily `security` log channel, configured from the base path
   `storage/logs/security.log` and written as date-suffixed files, for
   operational collection and alerting.

`SecurityAuditLogger` creates both records from the same event. A small set of
account administration and MFA lifecycle events currently uses `ActivityLogger`
directly and therefore appears only in `activity_logs`. The catalog below makes
that distinction explicit.

## Common Structured Metadata

Events emitted through `SecurityAuditLogger` include the following fields when
available:

| Field             | Description                                                            |
| ----------------- | ---------------------------------------------------------------------- |
| `outcome`         | Result such as `success`, `failure`, `blocked`, or `pending_mfa`       |
| `ip_address`      | Request source IP as resolved by Laravel's trusted-proxy configuration |
| `user_agent`      | User agent truncated to 500 characters                                 |
| `session_id_hash` | SHA-256 hash of the session ID, never the raw session credential       |
| `route`           | Laravel route name                                                     |
| `subject_user_id` | Account affected by the event, when known                              |
| `actor_user_id`   | Authenticated account that performed the action, when known            |
| `audit_log_id`    | Database activity-log identifier included in the file log              |

Event-specific metadata may add `provider`, `method`, `reason`, `remembered`,
`revoked_sessions`, or a keyed identity hash.

Unknown or unauthenticated login identities are represented by an HMAC-SHA-256
hash derived from the normalized email and `APP_KEY`. Do not replace it with a
raw email address.

## Event Catalog

| Event                               | Trigger                                            | Outcome or important metadata                 | Destinations              |
| ----------------------------------- | -------------------------------------------------- | --------------------------------------------- | ------------------------- |
| `auth.registered`                   | Password account created                           | `provider=password`                           | Database and security log |
| `auth.email_verification_requested` | Verification link resent                           | `success`                                     | Database and security log |
| `auth.email_verified`               | Signed verification link fulfilled                 | `success`                                     | Database and security log |
| `auth.login_failed`                 | Password credentials rejected                      | `failure`, identity hash, `provider=password` | Database and security log |
| `auth.login_blocked`                | Correct first factor supplied for disabled account | `blocked`, provider                           | Database and security log |
| `auth.first_factor_passed`          | Password accepted but MFA is required              | `pending_mfa`, `provider=password`            | Database and security log |
| `auth.login_succeeded`              | Password, GitHub, or MFA-protected login completed | provider and remember flag                    | Database and security log |
| `auth.oauth_failed`                 | GitHub OAuth or linking failed                     | provider and normalized reason code           | Database and security log |
| `auth.logout`                       | Current session signed out                         | `success`                                     | Database and security log |
| `auth.password_reset`               | Recovery token used successfully                   | all sessions revoked                          | Database and security log |
| `auth.password_changed`             | Authenticated password replaced                    | revoked-session count                         | Database and security log |
| `auth.session_revoked`              | One owned device session revoked                   | `success`                                     | Database and security log |
| `auth.other_sessions_revoked`       | All other owned sessions revoked                   | revoked-session count                         | Database and security log |
| `security.mfa_challenge_failed`     | MFA login proof rejected                           | `failure`                                     | Database and security log |
| `security.mfa_challenge_passed`     | MFA login proof consumed                           | remember flag                                 | Database and security log |
| `security.step_up_failed`           | Recent-auth proof rejected                         | method                                        | Database and security log |
| `security.step_up_succeeded`        | Recent-auth proof accepted                         | method                                        | Database and security log |
| `security.github_connected`         | GitHub identity explicitly connected               | `provider=github`                             | Database and security log |
| `security.mfa_enabled`              | MFA enrollment confirmed                           | lifecycle event                               | Database only             |
| `security.mfa_disabled`             | MFA removed after step-up                          | lifecycle event                               | Database only             |
| `user_login.disabled`               | Admin suspends a standard account                  | actor and subject                             | Database only             |
| `user_login.enabled`                | Admin restores a standard account                  | actor and subject                             | Database only             |
| `admin_access.requested`            | User requests administrator access                 | actor and subject                             | Database only             |
| `admin_access.granted`              | Admin approves administrator access                | actor and subject                             | Database only             |
| `admin_access.revoked`              | Admin removes another admin role                   | actor and subject                             | Database only             |

## OAuth Failure Reason Codes

`auth.oauth_failed` uses stable reason codes suitable for dashboards and alerts:

| Reason                      | Meaning                                                                             |
| --------------------------- | ----------------------------------------------------------------------------------- |
| `invalid_state`             | Missing or mismatched session-bound OAuth state                                     |
| `provider_denied`           | User or provider denied authorization                                               |
| `token_exchange_failed`     | GitHub code exchange failed or omitted an access token                              |
| `profile_fetch_failed`      | GitHub profile API request failed                                                   |
| `invalid_identity`          | GitHub did not supply a usable ID, username, and verified primary email             |
| `identity_conflict`         | GitHub ID and email ownership conflict across local users                           |
| `explicit_link_required`    | Verified email belongs to an existing account that has not explicitly linked GitHub |
| `login_disabled`            | The linked local account is suspended                                               |
| `step_up_required`          | GitHub linking was attempted without recent authentication                          |
| `step_up_identity_mismatch` | GitHub step-up returned a different identity                                        |
| `authentication_required`   | Linking callback lacks the expected authenticated account                           |
| `email_conflict`            | Linking would claim an email owned by another account                               |

## Data Classification and Prohibited Fields

Audit metadata contains security telemetry and personal data. Restrict database
and file-log access to authorized operators. IP addresses and user agents require
a documented retention and privacy policy in production.

Never add any of the following to an event, exception message, URL, or log
context:

- plaintext passwords or password confirmation;
- password-reset tokens or signed verification URLs;
- OAuth authorization codes, access tokens, or client secrets;
- TOTP seeds, TOTP values, provisioning URIs, or QR code payloads;
- recovery codes or their hashes;
- raw session IDs, remember tokens, or cookie values;
- `APP_KEY` or other application secrets.

## Monitoring Recommendations

At minimum, aggregate and alert on:

- a sharp increase in `auth.login_failed` by IP, identity hash, or time window;
- any sustained `auth.login_blocked` activity;
- repeated `security.mfa_challenge_failed` or `security.step_up_failed` events;
- `auth.oauth_failed` grouped by reason, especially `invalid_state`, identity
  conflicts, or callback failures;
- password reset followed by GitHub linking, MFA disablement, or role changes;
- administrator access and login-status changes;
- unexpected absence of security log records while authentication traffic
  continues.

File-log retention defaults to 90 days through `SECURITY_LOG_DAYS`. Database
activity retention is not automated and must be defined before production use.
