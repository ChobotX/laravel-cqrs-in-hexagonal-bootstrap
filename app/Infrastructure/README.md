# Infrastructure Layer

Concrete implementations of contracts and framework integrations. All classes must be `final`.

## BusServiceProvider registration

All command, query, and event handler mappings are registered in `app/Infrastructure/Provider/BusServiceProvider.php`.

```php
// Commands
{Name}Command::class => {Name}Handler::class,

// Queries
{Name}Query::class => {Name}Handler::class,

// Events
{EventClass}::class => [{HandlerClass}::class],
```

Every new command, query, or event handler must be registered here. See [Domain README](../Domain/README.md) for full CQRS walkthrough.

## Scope Resolution Middleware

`ResolveScopeFilter` is a query bus middleware that transparently enriches `ScopeAwareQuery` objects with the actor's access scope. It runs after `AuthorizeAction` in the middleware pipeline.

**Pipeline order:** `AuthorizeAction` → `ResolveScopeFilter` → Handler

For queries implementing `ScopeAwareQuery`, the middleware:
1. Reads `#[RequiresPermission]` attribute via reflection
2. Calls `AuthorizationChecker::canWithScope()` to get the scope
3. For `Team` scope: resolves visible IDs via `TeamMembershipChecker::visibleUserIds()`
4. Creates a new query instance via `withAccessContext()` (immutable copy pattern — query objects are `final readonly`)

Non-`ScopeAwareQuery` queries pass through unchanged.

## Logging

`App\Infrastructure\Logging\LaravelLogger` implements `App\Contract\Logging\Logger`, wrapping Laravel's `Log` facade. This is the only place in the application where `Log::` is called directly — all other code must inject the `Logger` interface. Enforced by PHPStan rule `NoDirectLoggingRule`.

## Repository + Mapper pattern

Repositories implement contract interfaces and use Eloquent models internally. Domain objects are never Eloquent models — mappers translate between Eloquent models and domain objects.

The `PaginatesQuery` trait (`app/Infrastructure/Eloquent/PaginatesQuery.php`) provides a `paginateBuilder()` helper that Eloquent repositories use for paginated list methods. It executes a `COUNT(*)` + offset/limit query and returns `[list<Model>, int $total]` for the caller to map and wrap in `PaginatedResult`.

The `SortsQuery` trait (`app/Infrastructure/Eloquent/SortsQuery.php`) provides a `sortBuilder()` helper that applies `ORDER BY` to an Eloquent builder. Each repository using the trait must implement `textSortColumns()` returning column names where case-insensitive sorting via `LOWER()` applies. Non-text columns (timestamps, numerics) get plain `ORDER BY`. For computed columns like `permission_score`, repositories add `selectRaw` with the SQL expression before calling `sortBuilder()`.

## Tenant Schema Management

Schema-based multi-tenancy is implemented in `App\Infrastructure\Tenancy\`. See [Tenancy/README.md](Tenancy/README.md) for full details.

Key classes:
- `TenantSchemaManager` — switches `tenant` connection's `search_path` per request. Skips purge when connection details haven't changed.
- `TenantResolver` — resolves tenant by domain or slug via landlord DB
- `TenantBootstrapperImpl` — composes resolver + schema manager + context. Implements `TenantBootstrapper` contract so Presentation never imports Infrastructure directly.
- `ResolvedTenantContext` — mutable scoped singleton implementing `TenantContext`
- `TenantMigrator` — creates schema + runs tenant migrations
- `ConsoleTenantBootstrap` — event listener for CLI tenant resolution via `#[TenantAwareCommand]`

The Infrastructure layer may depend on Application, Domain, and Contract. It must not depend on Presentation.
