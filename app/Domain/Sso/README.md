# SSO Module

Tenant-configurable single sign-on. Each tenant may register one or more SSO
providers (OIDC, OAuth2 social, SAML 2.0), optionally enforce SSO over
password login, and pick how unknown subjects are provisioned.

## Concepts

- **`SsoConfiguration`** — tenant-scoped, encrypted-at-rest provider record.
  `provider_type + slug` is unique. `enabled` and `enforce` toggle visibility
  and password-login lockout independently. `jit_mode` chooses how
  unknown identities are handled.
- **`UserSsoIdentity`** — link row binding an internal user to an IdP subject
  for one configuration. Hard-deleted with the configuration.
- **`SsoAuthenticator`** — port describing per-protocol behaviour:
  `initiate(SsoConfiguration): RedirectInstruction`,
  `complete(SsoConfiguration, payload): SsoIdentity`,
  `probe(SsoConfiguration): SsoConnectionTestResult`.
- **`SsoAuthenticatorRegistry`** — selects the implementing adapter for the
  resolved `ProviderType`.

## JitMode

| Mode | What happens for an unknown subject |
|------|-------------------------------------|
| `invited_only` | Login proceeds only if a user with the asserted email already exists. Pending users are activated via `MarkUserActivatedCommand`. |
| `auto_create` | New user created via `CreateUserCommand` if the email domain is in `allowed_email_domains` (or the list is empty). The user is also activated. |
| `linked_only` | Login is rejected unless an admin previously linked the IdP subject to a user. |

## Login flow

1. Login page lists enabled providers via `GetEnabledSsoProvidersQuery` (skips permission check).
2. The presentation layer redirects the browser to `RedirectInstruction::url`
   returned by `initiate()`.
3. The IdP redirects back to `/auth/sso/{slug}/callback` with the protocol
   payload. The presentation controller dispatches `LoginViaSsoCommand` with
   the raw payload.
4. `LoginViaSsoHandler` resolves the configuration, calls the registered
   authenticator's `complete()`, looks up an existing identity, applies
   `jitMode` if missing, then persists the link via `LinkSsoIdentityCommand`.
5. `SsoLoginSucceeded` / `SsoLoginFailed` events are emitted in every path so
   the audit log captures the outcome.

## Enforcement

When at least one enabled configuration has `enforce = true`, password-based
login for non-admins is rejected. The presentation `LoginController` checks
`SsoConfigurationRepository::hasEnforcedConfiguration()` and routes the user
to the SSO flow.

## Cross-domain dependencies

The `LoginViaSsoHandler` depends on the public User Contract:
`UserRepository`, `CreateUserCommand`, `MarkUserActivatedCommand`. New
provisioning steps therefore go through the bus, never through direct
repository writes.

## Permissions

Configured under `sso.management` (read/create/update/delete/test) and
`sso.identities.unlink`. See
[Authorization README](../Authorization/README.md).

## Feature flag

The whole module is gated behind the `sso.enabled` flag (see
[FeatureFlag README](../FeatureFlag/README.md)). Tenants opt in
individually.
