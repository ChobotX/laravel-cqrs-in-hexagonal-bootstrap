# Tenancy Infrastructure

PostgreSQL schema-based multi-tenancy. Each tenant is a self-contained schema with zero cross-tenant data access.

## Architecture

```
Landlord Schema (minimal)              Tenant Schema (per-tenant, FULL isolation)
+------------------------------+       +----------------------------------+
| tenants                      |       | users, personal_access_tokens    |
| tenant_domains               |       | tenant_preferences               |
|                              |       | password_rotation_settings       |
|                              |       | user_password_history            |
+------------------------------+       | teams, team_members              |
                                       | roles, role_permissions          |
                                       | user_roles, user_permission_...  |
                                       | record_shares, permission_audit  |
                                       | impersonation_sessions           |
                                       | jobs, failed_jobs, cache         |
                                       +----------------------------------+
```

## Database Connections

- `landlord` — fixed `search_path = 'landlord'`, used only for tenant resolution
- `tenant` (default) — dynamic `search_path`, switched per-request to active tenant's schema

## Request Flow

1. `ResolveTenantMiddleware` extracts subdomain from `Host` header
2. `TenantBootstrapper::bootstrapByDomain()` resolves tenant via DB lookup and switches schema
3. All subsequent queries transparently hit the correct tenant schema

## Key Classes

| Class | Layer | Purpose |
|---|---|---|
| `TenantPreferencesSingletonMissingException` | Infrastructure | Thrown when `tenant_preferences` id `1` is missing after migrations (programming or migration failure) |
| `TenantDisplayPreferencesSync` | Infrastructure | After migrations, ensures `tenant_preferences.display_name` is set from an explicit migrate name or from the tenant slug headline when empty |
| `TenantContext` | Contract | Current tenant ID/slug/display name/logo URL / optional display timezone (IANA) |
| `TenantBootstrapper` | Contract | Resolve + switch schema (interface for Presentation) |
| `TenantSchemaManager` | Infrastructure | Configures tenant DB connection and manages schemas |
| `TenantResolver` | Infrastructure | Resolves tenant by domain or slug via landlord DB |
| `ResolvedTenantContext` | Infrastructure | Mutable scoped singleton implementing TenantContext |
| `TenantMigrator` | Infrastructure | Creates schema + runs tenant migrations; leaves the `tenant` connection on that schema (call `TenantBootstrapper::reset()` / `TenantSchemaManager::reset()` when a neutral `search_path` is required) |
| `ConsoleTenantBootstrap` | Infrastructure | Event listener for CLI tenant resolution |
| `TenantBootstrapperImpl` | Infrastructure | Implements TenantBootstrapper contract |
| `EloquentTenantSettingsRepository` | Infrastructure | Reads/writes organization display name and logo in tenant schema (`tenant_preferences`) via public disk; reads/writes display timezone there; landlord `tenants` holds routing metadata only |

## Console Commands

| Command | Attribute | Description |
|---|---|---|
| `tenant:setup` | `TenantAgnosticCommand` | Full setup: landlord + tenants + migrations + seeds + `storage:link` for public tenant logos |
| `tenant:create` | `TenantAgnosticCommand` | Create a new tenant with schema |
| `tenant:migrate` | `TenantAgnosticCommand` | Run migrations for one or all tenants; `MigrateTenantHandler` / `MigrateAllTenantsHandler` reset tenant connection scope afterward |

All other console commands (e.g. `user:create`) use `#[TenantAwareCommand]` and require `--tenant=slug`.

## Adding a New Tenant

```bash
./vendor/bin/sail php artisan tenant:create "Acme Corp" acme --domain=acme
```

This inserts the tenant record, creates the PostgreSQL schema, and runs all tenant migrations.

## Queue Jobs

`HandleDomainEventJob` serializes the current tenant slug alongside the domain event. When the queue worker processes the job, it calls `TenantBootstrapper::bootstrapBySlug()` to restore tenant context before executing the handler. This ensures event handlers have the correct tenant schema active regardless of worker configuration.

Queue connections use `after_commit: true` — jobs are only dispatched to the queue after the triggering database transaction commits, preventing handlers from reading uncommitted data.

## Domain Isolation

Non-tenancy domain contexts are tenant-agnostic. Enforced by PHPat rule `testNonTenancyDomainDoesNotDependOnTenancy` — `App\Domain\*` cannot import `App\Domain\Tenancy\Contract\Service\*` (except `Domain\Tenancy` itself and the explicitly allowed `EmailTemplate\Service`, `FeatureFlag\Service`).
