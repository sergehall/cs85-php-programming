# Authentication Testing Strategy

## Objectives

Authentication tests must prove both successful user journeys and rejection of
attacker-controlled or stale state. Route rendering alone is not sufficient.
Tests should assert authentication state, redirects or status codes, database
effects, session invalidation, notification dispatch, and audit evidence.

The current suite uses Laravel feature tests with isolated databases through
`RefreshDatabase`. External GitHub requests and notifications are faked.

## Test Ownership Matrix

| Test file                                       | Primary coverage                                                                                                                  |
| ----------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------- |
| `tests/Feature/AuthFlowTest.php`                | Registration, password login, disabled accounts, logout, base GitHub sign-in                                                      |
| `tests/Feature/AuthSecurityTest.php`            | Invalid credentials, weak passwords, duplicates, confirmation mismatch, logout access                                             |
| `tests/Feature/AuthenticationHardeningTest.php` | Email normalization and verification, reset revocation, rate limits, audit secrecy, step-up, MFA expiry/replay, session ownership |
| `tests/Feature/GitHubOAuthSecurityTest.php`     | OAuth state, provider failures, verified email, explicit linking, identity conflicts, disabled users, GitHub step-up              |
| `tests/Feature/MfaAuthenticationTest.php`       | Enrollment, sign-in challenge, invalid proof, single-use recovery code, and disablement                                           |
| `tests/Feature/AuthorizationMatrixTest.php`     | Guest cabinet denial and user/admin route boundaries                                                                              |
| `tests/Feature/AdminAccessRequestTest.php`      | Role lifecycle, login suspension, protected admin actions, and session revocation                                                 |
| `tests/Feature/ActivityTimelineTest.php`        | User/admin visibility and lifecycle audit records                                                                                 |
| `tests/Feature/SecurityHeadersTest.php`         | Web security headers on authentication surfaces                                                                                   |
| `tests/Feature/BladeSecuritySurfaceTest.php`    | CSRF and safe rendering invariants in Blade forms                                                                                 |

## Required Behavior Matrix

Every authentication release should retain coverage for these cases:

| Area               | Positive cases                                              | Negative and abuse cases                                                                                        |
| ------------------ | ----------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------- |
| Registration       | Normalized unique email, strong password, notification sent | Weak password, duplicate email, mismatch, throttling                                                            |
| Email verification | Valid signed link unlocks cabinet                           | Unverified cabinet access, invalid signature, resend throttling                                                 |
| Password login     | Valid login and session regeneration                        | Invalid credentials, password-disabled account, admin-disabled account, throttling                              |
| Password recovery  | Case-normalized request and valid reset                     | Invalid/expired token, weak replacement, enumeration resistance, throttling                                     |
| GitHub OAuth       | New OAuth-only account, explicit linking, MFA handoff       | Missing/mismatched state, provider failure, unverified email, silent merge, identity conflict, disabled account |
| MFA                | Enrollment, TOTP challenge, one recovery-code use           | Expired challenge, invalid value, replayed TOTP, reused recovery code, missing setup state                      |
| Step-up            | Password, MFA, and eligible GitHub proof                    | Invalid proof, expired proof, different user/session, weaker-method downgrade                                   |
| Sessions           | Revoke owned session and all other sessions                 | Current-session single revoke, another user's session ID, missing session                                       |
| Authorization      | Verified user cabinet and admin access                      | Guest access, unverified access, disabled user, standard user on admin routes                                   |
| Auditing           | Expected event, actor/subject, outcome, and safe metadata   | Passwords, tokens, codes, secrets, raw session IDs, and raw unknown identities absent                           |

## Required Quality Gates

Run the complete gates from the repository root:

```bash
php artisan test
composer lint
vendor/bin/pint --test
npm run quality
git diff --check
```

The commands cover PHPUnit, Larastan/PHPStan, PHP formatting, Markdown and asset
formatting, Node tests, the CI secret guard, and the production Vite build.

Targeted authentication runs are useful during development but do not replace
the complete suite:

```bash
php artisan test tests/Feature/AuthFlowTest.php
php artisan test tests/Feature/AuthenticationHardeningTest.php
php artisan test tests/Feature/GitHubOAuthSecurityTest.php
php artisan test tests/Feature/MfaAuthenticationTest.php
php artisan test --filter=AuthenticationHardeningTest
```

## Test Design Rules

### Assert State, Not Only Responses

A redirect can hide an unsafe state transition. Assert the relevant model,
session, and database records after every security operation. Examples include:

- `assertAuthenticatedAs()` or `assertGuest()`;
- verified timestamps and login flags;
- removed `sessions` rows;
- rotated or changed credentials;
- consumed recovery-code lists;
- `mfa_last_used_time_slice` advancement;
- expected `activity_logs` rows.

### Test Both Sides of Every Boundary

For each protected action, test at least:

1. guest;
2. authenticated but unverified user;
3. verified standard user;
4. administrator when relevant;
5. valid recent-auth proof;
6. missing, invalid, or expired proof;
7. another user's resource identifier.

### Control Time Explicitly

MFA challenges and step-up state are time-bound. Use Laravel's time helpers to
test immediately valid, boundary, and expired states. Do not rely on real sleeps.
TOTP replay tests must consume a code and prove the same time slice is rejected.

### Fake External Boundaries

Use Laravel HTTP fakes for every GitHub endpoint and notification fakes for email.
Tests must not depend on network access, a real GitHub account, or an external
mailbox. Assert request URL, state, relevant request fields, timeout-sensitive
failure handling, and the primary verified email selection behavior.

Never place real client secrets, access tokens, email credentials, or production
identities in fixtures.

### Verify Audit Secrecy

Audit tests must assert expected event names and also serialize metadata to prove
that submitted passwords, OAuth codes/tokens, TOTP values, recovery codes, and raw
session IDs are absent. Testing only event existence is incomplete.

### Verify Session Invalidation

Use the database session driver in session-revocation tests and insert distinct
sessions for the actor, the same account, and another account. Assert exact rows
removed or retained. Include remember-token behavior when changing the session
service.

## Adding or Changing Authentication Behavior

Use this checklist in the implementation pull request:

- [ ] Add a positive feature test for the intended user journey.
- [ ] Add invalid-input and attacker-controlled-state tests.
- [ ] Add rate-limit coverage for a new public or sensitive write route.
- [ ] Test guest, verification, account-status, role, and step-up middleware as applicable.
- [ ] Test session regeneration or invalidation at the trust transition.
- [ ] Test actor/resource ownership and IDOR resistance.
- [ ] Test audit event content and prohibited-field absence.
- [ ] Fake mail and external HTTP boundaries.
- [ ] Update route, event, configuration, operations, and limitation documentation.
- [ ] Run every required quality gate.

## Manual Review Checklist

Automated tests do not replace review of deployment and human workflows. Before a
security-sensitive release, manually inspect:

- form CSRF tokens and absence of secrets in HTML, URLs, and browser storage;
- redirect destinations and intended-URL handling;
- generic errors that avoid account enumeration;
- email link origin, expiry behavior, and deliverability;
- OAuth consent, callback, cancellation, and wrong-GitHub-account recovery;
- QR and recovery-code one-time display behavior;
- multi-device session labels and revocation feedback;
- activity visibility for users and administrators;
- centralized logs and alert delivery in the target environment;
- keyboard navigation and accessible error/status messaging.

## Regression Triage

When an auth test fails, do not weaken the assertion until the security invariant
is understood. Classify the failure as one of:

- intended product change requiring documentation and threat review;
- test isolation or fixture defect;
- framework/configuration drift;
- session/cache/mail/external-boundary mismatch;
- actual authentication or authorization regression.

Preserve a regression test for every confirmed security defect.
