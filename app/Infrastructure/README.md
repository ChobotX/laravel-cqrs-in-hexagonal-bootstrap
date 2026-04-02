# Infrastructure Layer

Concrete implementations of contracts and framework integrations. All classes must be `final`.

## BusServiceProvider registration

All command, query, and event handler mappings are registered in `app/Infrastructure/Provider/BusServiceProvider.php`. All handler implementations live in `App\Domain\` — Infrastructure only provides the wiring. Enforced by PHPStan rule `HandlersInDomainRule`.

```php
// Commands
{Name}Command::class => {Name}Handler::class,

// Queries
{Name}Query::class => {Name}Handler::class,

// Events
{EventClass}::class => [{HandlerClass}::class],
```

Every new command, query, or event handler must be registered here. See [Domain README](../Domain/README.md) for full CQRS walkthrough.

## Command Bus Middleware Pipeline

**Pipeline order:** `AuthorizeAction` -> `WrapInTransaction` -> `DispatchCollectedEvents` -> Handler

- `AuthorizeAction` -- checks `#[RequiresPermission]` attribute, runs outside the transaction for fast-fail
- `WrapInTransaction` -- wraps handler execution and event dispatch in a single database transaction on the default (`tenant`) connection. Nested `DB::transaction()` calls in repositories create PostgreSQL SAVEPOINTs transparently. Commands can opt out with `#[SkipTransaction(reason: '...')]`.
- `DispatchCollectedEvents` -- runs the handler, then flushes collected events to the async queue. Job rows are inserted within the same transaction, so handler writes and event jobs commit atomically.

## Scope Resolution Middleware

`ResolveScopeFilter` is a query bus middleware that transparently enriches `ScopeAwareQuery` objects with the actor's access scope. It runs after `AuthorizeAction` in the middleware pipeline.

**Pipeline order:** `AuthorizeAction` → `ResolveScopeFilter` → Handler

For queries implementing `ScopeAwareQuery`, the middleware:
1. Reads `#[RequiresPermission]` attribute via reflection
2. Calls `AuthorizationChecker::canWithScope()` to get the scope
3. For `Team` scope: resolves visible IDs via `TeamMembershipChecker::visibleUserIds()`
4. Creates a new query instance via `withAccessContext()` (immutable copy pattern — query objects are `final readonly`)

Non-`ScopeAwareQuery` queries pass through unchanged.

`CachedTeamMembershipChecker` is a request-scoped decorator (registered via `scoped()`) that memoizes `memberTeamIds()` and `visibleUserIds()` per userId within a single request. This prevents redundant DB calls when multiple scope-aware queries run in the same request.

## Logging

`App\Infrastructure\Logging\LaravelLogger` implements `App\Contract\Logging\Logger`, wrapping Laravel's `Log` facade. This is the only place in the application where `Log::` is called directly — all other code must inject the `Logger` interface. Enforced by PHPStan rule `NoDirectLoggingRule`.

## Repository + Mapper pattern

Repositories implement contract interfaces and use Eloquent models internally. Domain objects are never Eloquent models — mappers translate between Eloquent models and domain objects.

The `HasOptimisticLocking` trait (`app/Infrastructure/Eloquent/HasOptimisticLocking.php`) provides optimistic concurrency control. It overrides `performUpdate()` to add `WHERE version = ?` and increment the version column atomically. If 0 rows are affected (stale version), it throws `ConcurrentModificationException` (HTTP 409). The version is set to 1 on creation automatically.

The `PaginatesQuery` trait (`app/Infrastructure/Eloquent/PaginatesQuery.php`) provides a `paginateBuilder()` helper that Eloquent repositories use for paginated list methods. It executes a `COUNT(*)` + offset/limit query and returns `[list<Model>, int $total]` for the caller to map and wrap in `PaginatedResult`.

The `SortsQuery` trait (`app/Infrastructure/Eloquent/SortsQuery.php`) provides a `sortBuilder()` helper that applies `ORDER BY` to an Eloquent builder. Each repository using the trait must implement `textSortColumns()` returning column names where case-insensitive sorting via `LOWER()` applies. Non-text columns (timestamps, numerics) get plain `ORDER BY`. For computed columns like `permission_score`, repositories add `selectRaw` with the SQL expression before calling `sortBuilder()`.

## File Storage

`App\Infrastructure\Filesystem\LaravelFileStorage` implements `App\Domain\File\Contract\FileStorage`, wrapping Laravel's `Illuminate\Contracts\Filesystem\Filesystem`. This is the only place in the application that touches the filesystem directly — all other code must inject the `FileStorage` interface. Storage paths use `{namespace}/{uuid}.{extension}` format. Files are streamed via `SplFileInfo::getPathname()` to avoid loading full contents into memory.

A dedicated `files` disk is configured in `config/filesystems.php`. Swapping storage backends (local ↔ S3) requires only changing `FILES_DISK_DRIVER` in `.env` — no code changes. The disk is resolved via `FilesystemFactory::disk('files')` in `RepositoryServiceProvider`.

Enforced by PHPStan rules `NoDirectFilesystemAccessRule` (bans facade calls and PHP file functions) and `NoDirectFilesystemImportRule` (bans framework filesystem imports) outside `Infrastructure\Filesystem\`.

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
