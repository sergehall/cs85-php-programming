# Authentication Operations Runbook

## Production Configuration Baseline

Authentication depends on application, session, mail, cache, logging, and GitHub
OAuth configuration. Secrets belong in the deployment secret store, never in the
repository.

| Variable                 | Production guidance                                                                                                                   |
| ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------- |
| `APP_KEY`                | Generate once, store as a high-value encryption secret, back up securely, and rotate only with a data migration plan                  |
| `APP_URL`                | Set to the canonical HTTPS origin used in signed links and OAuth callbacks                                                            |
| `APP_ENV`                | Set to `production`                                                                                                                   |
| `APP_DEBUG`              | Set to `false`                                                                                                                        |
| `SESSION_DRIVER`         | Use `database` for the implemented device list and revocation features, or extend the session service before selecting another driver |
| `SESSION_SECURE_COOKIE`  | Set to `true` under HTTPS                                                                                                             |
| `SESSION_HTTP_ONLY`      | Keep `true`                                                                                                                           |
| `SESSION_SAME_SITE`      | Keep `lax` unless a reviewed cross-site requirement justifies another value                                                           |
| `SESSION_LIFETIME`       | Default is 120 idle minutes; adjust only through a documented risk decision                                                           |
| `AUTH_MFA_CHALLENGE_TTL` | Default is 300 seconds                                                                                                                |
| `AUTH_STEP_UP_TTL`       | Default is 900 seconds                                                                                                                |
| `SECURITY_LOG_LEVEL`     | Keep `info` or stricter without suppressing expected auth events                                                                      |
| `SECURITY_LOG_DAYS`      | Default file retention is 90 days; align with privacy and incident-response requirements                                              |
| `MAIL_*`                 | Configure a deliverable production mail transport and monitored sender domain                                                         |
| `GITHUB_CLIENT_ID`       | GitHub OAuth application client ID                                                                                                    |
| `GITHUB_CLIENT_SECRET`   | Secret-store value for the GitHub OAuth application                                                                                   |
| `GITHUB_REDIRECT_URI`    | Exact canonical callback: `${APP_URL}/auth/github/callback`                                                                           |

Use a shared cache or rate-limit store for horizontally scaled deployments.
Otherwise, each instance may enforce an independent request budget.

## GitHub OAuth Setup

1. Create or select the GitHub OAuth application for the deployment environment.
2. Set its homepage URL to the canonical `APP_URL`.
3. Set its authorization callback URL to the exact
   `GITHUB_REDIRECT_URI`; scheme, host, port, path, and trailing slash must match.
4. Store the client ID and client secret in the deployment secret store.
5. Clear and rebuild Laravel's configuration cache.
6. Test new-user sign-in, existing-account explicit linking, cancellation,
   identity conflict, disabled-account rejection, and MFA handoff.

Use separate OAuth applications and credentials for development, staging, and
production. Never reuse production client secrets locally.

## Mail Setup

Email verification and password recovery are operational dependencies, not
optional UI enhancements.

Before release:

- authenticate the sender domain with the mail provider;
- verify `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`;
- confirm HTTPS links use the canonical production host;
- test delivery to multiple mailbox providers;
- monitor bounces, blocks, and provider errors;
- ensure application logs do not expose signed links or reset tokens;
- document support handling for delayed or rejected messages.

## Deployment Procedure

Run the authentication-specific release sequence after the normal application
build and before directing production traffic to the release:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan route:list --path=login
php artisan route:list --path=cabinet/security
```

The hardening migration normalizes stored emails before adding new authentication
fields. It intentionally fails if case-insensitive duplicates exist. Resolve any
collision through an approved account-merge or rename procedure before retrying;
do not delete an account merely to make the migration pass.

The public UUID hardening migration backfills missing or invalid user UUIDs and
then makes `users.public_uuid` non-nullable. Keep the bigint primary key for
internal joins; verify that user-facing and email-verification URLs contain the
UUID instead of that numeric key.

Confirm that the deployment can write to `storage/logs` and that the database
contains the `users`, `sessions`, `password_reset_tokens`, and `activity_logs`
tables.

## Release Verification

Perform these checks in staging and a production-safe smoke test:

1. Register a new account and confirm cabinet access is blocked until email is
   verified.
2. Verify the signed email link contains the account UUID, not the internal
   numeric ID, and confirm cabinet access.
3. Sign out and sign back in with normalized email casing.
4. Request and complete a password reset; confirm old sessions are gone.
5. Link GitHub only after recent-auth confirmation; confirm an email match alone
   cannot merge accounts.
6. Enable MFA, store the displayed recovery codes, sign out, and complete an MFA
   login.
7. Confirm reuse of the same TOTP value or recovery code is rejected.
8. Revoke another owned session and confirm another user's session identifier
   cannot be revoked.
9. Verify security events appear in `activity_logs` and `security.log` without
   credentials or tokens.
10. Confirm rate-limited endpoints return HTTP 429 after their configured budget.

Use dedicated test identities. Do not run destructive session or MFA checks on a
human operator's only production administrator account.

## Routine Monitoring

Useful local inspection commands:

```bash
ls -lt storage/logs/security-*.log
tail -f storage/logs/security-YYYY-MM-DD.log
php artisan route:list --path=auth
php artisan route:list --path=security
php artisan about
```

For production, ship the security log to centralized, access-controlled storage
and correlate on `audit_log_id`, `subject_user_id`, `actor_user_id`, event,
outcome, IP, and time. Alerting guidance is maintained in
[Audit Events](audit-events.md#monitoring-recommendations).

Monitor these service indicators:

- login, MFA, OAuth, verification, and password-reset success rates;
- HTTP 429 volume by authentication route;
- mail delivery and bounce rates;
- OAuth callback latency and provider errors;
- security-log ingestion lag;
- database session and activity-log growth;
- unexpected increases in administrator access changes.

## Incident Response

### Suspected Standard-User Account Compromise

1. From a recently confirmed administrator session, disable login for the
   standard user. This revokes all database sessions and remember-me access.
2. Preserve relevant `activity_logs`, centralized security logs, mail-provider
   records, and reverse-proxy logs.
3. Review password resets, GitHub linking, MFA changes, session revocations, and
   role changes around the suspected time window.
4. Verify the user's recovery email channel and GitHub account outside the
   compromised session.
5. Restore access only after password reset, external-provider review, and MFA
   re-enrollment as required.
6. Record the incident timeline and remediation outside mutable application
   activity data.

### Suspected Administrator Account Compromise

The admin UI intentionally prevents disabling an administrator through the
standard-user suspension action and prevents self-role revocation. Use a
peer-approved emergency procedure:

1. restrict application access at the edge if active abuse is occurring;
2. preserve evidence;
3. use a controlled database transaction or temporary reviewed command to
   demote or suspend the account;
4. revoke every session and rotate remember-me state;
5. rotate affected credentials and verify other administrator accounts;
6. remove the temporary intervention and add regression tests if a permanent
   product workflow is required.

Never improvise an unaudited permanent bypass.

### GitHub OAuth Secret Exposure

1. Rotate the client secret in GitHub immediately.
2. update the deployment secret store;
3. rebuild Laravel's configuration cache and restart application instances;
4. inspect `auth.oauth_failed` and `security.github_connected` events;
5. verify callback configuration and complete a controlled OAuth smoke test.

The application does not persist GitHub access tokens, reducing the local token
revocation surface.

### `APP_KEY` Exposure or Rotation

Treat `APP_KEY` exposure as critical. It protects Laravel encrypted data,
including MFA secrets and encrypted recovery-code arrays, and can affect cookies
and signed/encrypted application values.

Before rotation, inventory encrypted fields and define a migration or forced-reset
strategy. Rotating the key without that plan can make existing MFA data
undecryptable. A safe response will normally require invalidating sessions,
forcing MFA re-enrollment, reviewing signed-link exposure, deploying the new key,
and validating that no old instance still serves traffic.

### Brute Force or MFA Abuse

1. verify rate-limit storage is healthy and shared by all instances;
2. group failures by IP, identity hash, subject user, route, and user agent;
3. apply temporary edge controls when application limits are insufficient;
4. avoid revealing whether a submitted email exists;
5. preserve samples and tune limits only after measuring legitimate failure rates.

### Mail Delivery Failure

1. inspect mail-provider health, credentials, sender-domain status, and bounces;
2. verify `APP_URL` and sender configuration after config-cache rebuild;
3. keep generic password-recovery responses in place;
4. do not send reset tokens manually through chat, tickets, or logs;
5. use the supported resend route after delivery is restored.

## Backup, Restore, and Retention

Back up `users`, `sessions`, `password_reset_tokens`, and `activity_logs` according
to their classification and recovery objectives. Protect backups with encryption
and access auditing.

Restore testing must confirm:

- encrypted MFA fields remain decryptable with the restored `APP_KEY`;
- session data is either intentionally restored or intentionally invalidated;
- reset tokens and signed-link behavior do not create an unexpected replay
  window;
- activity history remains queryable and correctly associated with users.

Define separate retention periods for application activity, centralized security
logs, sessions, and password-reset tokens. The repository currently automates
only daily file-log retention.

## Troubleshooting

| Symptom                                 | Checks                                                                                                                      |
| --------------------------------------- | --------------------------------------------------------------------------------------------------------------------------- |
| GitHub reports redirect mismatch        | Compare GitHub callback URL, `APP_URL`, `GITHUB_REDIRECT_URI`, HTTPS scheme, and cached configuration                       |
| Verification/reset link uses wrong host | Check `APP_URL`, proxy headers, mail generation environment, and config cache                                               |
| Session list is empty                   | Confirm `SESSION_DRIVER=database`, migration state, and authenticated session persistence                                   |
| Revoked user remains active             | Confirm all instances share the same session database and remember token was rotated                                        |
| MFA code always fails                   | Check server clock synchronization, account secret integrity, six-digit input, and allowed one-slice window                 |
| Valid MFA code works once only          | Expected replay prevention; wait for the next 30-second TOTP period                                                         |
| Security event missing from file log    | Determine whether the event uses `ActivityLogger` only; then check storage permissions and `security` channel configuration |
| Rate limits differ between instances    | Configure a shared cache/rate-limit store and verify consistent cache prefixes                                              |

## Rollback Guidance

Avoid rolling application code back across an authentication schema change
without verifying model compatibility. A rollback must preserve:

- normalized unique email identities;
- the ability to decrypt MFA fields;
- session and remember-token invalidation semantics;
- audit evidence created during the release;
- compatibility with accounts created through GitHub-only login.

Prefer a forward fix. If rollback is unavoidable, disable affected entry points
at the edge, preserve evidence, test against a production-like database copy, and
document the exact account states that require remediation.
