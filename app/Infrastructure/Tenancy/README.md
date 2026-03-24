# Tenancy Infrastructure

PostgreSQL schema-based multi-tenancy. Each tenant is a self-contained schema with zero cross-tenant data access.

## Architecture

```
Landlord Schema (minimal)              Tenant Schema (per-tenant, FULL isolation)
+------------------------------+       +----------------------------------+
| tenants                      |       | users, personal_access_tokens    |
| tenant_domains               |       | teams, team_members              |
+------------------------------+       | roles, role_permissions          |
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
| `TenantContext` | Contract | Current tenant ID/slug |
| `TenantBootstrapper` | Contract | Resolve + switch schema (interface for Presentation) |
| `TenantSchemaManager` | Infrastructure | Configures tenant DB connection and manages schemas |
| `TenantResolver` | Infrastructure | Resolves tenant by domain or slug via landlord DB |
| `ResolvedTenantContext` | Infrastructure | Mutable scoped singleton implementing TenantContext |
| `TenantMigrator` | Infrastructure | Creates schema + runs tenant migrations |
| `ConsoleTenantBootstrap` | Infrastructure | Event listener for CLI tenant resolution |
| `TenantBootstrapperImpl` | Infrastructure | Implements TenantBootstrapper contract |

## Console Commands

| Command | Attribute | Description |
|---|---|---|
| `tenant:setup` | `TenantAgnosticCommand` | Full setup: landlord + tenants + migrations + seeds |
| `tenant:create` | `TenantAgnosticCommand` | Create a new tenant with schema |
| `tenant:migrate` | `TenantAgnosticCommand` | Run migrations for one or all tenants |

All other console commands (e.g. `user:create`) use `#[TenantAwareCommand]` and require `--tenant=slug`.

## Adding a New Tenant

```bash
./vendor/bin/sail php artisan tenant:create "Acme Corp" acme --domain=acme
```

This inserts the tenant record, creates the PostgreSQL schema, and runs all tenant migrations.

## Domain Isolation

The domain layer is fully tenant-agnostic. Enforced by PHPat rule `testDomainDoesNotDependOnTenancy` — `App\Domain\*` cannot import `App\Contract\Tenancy\*`.
