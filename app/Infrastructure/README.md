# Infrastructure Layer

Concrete implementations of contracts and framework integrations. All classes must be `final`.

## Thin adapters vs Domain orchestration

Infrastructure classes implement **ports** such as repositories, mailers, cache decorators, and framework bridges. They must not call `EventCollector::collect()`, must not type-hint `EventCollector` on properties or method return types (the collector is a domain-side concern), must not take `CommandBus` or `QueryBus` as dependencies (except under `Infrastructure\Bus\*` and `Infrastructure\Provider\*`, where the buses are implemented or wired), must not resolve buses via `app()` / `App::make()`, must not call translation helpers (`__`, `trans`, `trans_choice`), and must not implement multi-step workflows that span bounded contexts.

Outside those wiring and persistence namespaces, a `use` statement that pulls in another module’s code must import only from that module’s `Contract\` tree (`App\Domain\{X}\Contract\…`). Adapters that belong to the same domain module may still import internal types from that module, such as value objects, exceptions, or schema types.

Orchestration, raising domain events, and policy decisions stay in `App\Domain\` command handlers (or controller/CLI entrypoints before dispatch). Infrastructure does one thing per adapter and avoids multi-step coordination logic. PHPStan enforces this boundary with `NoInfrastructureEventCollectorCollectRule`, `NoInfrastructureEventCollectorInjectionRule`, `NoApplicationBusInInfrastructureRule`, `NoApplicationBusFromAppHelperInInfrastructureRule`, `NoTranslationHelperInInfrastructureRule`, `InfrastructureCrossDomainImportsRule`, and `InfrastructureCrossDomainGroupUseImportsRule`. For the architectural rationale, see [ADR.md](../../ADR.md) (section on thin Infrastructure adapters).

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

## Bus Middleware Pipeline

Middleware is business logic and lives outside Infrastructure. See [Application README](../Application/README.md) for shared middleware and [Authorization README](../Domain/Authorization/README.md) for authorization middleware. Enforced by PHPStan rule `testMiddlewareDoesNotLiveInInfrastructure`.

**Command pipeline:** `LogBusMessage` → `AuthorizeAction` → `WrapInTransaction` → `DispatchCollectedEvents` → Handler

**Query pipeline:** `AuthorizeAction` → `ResolveScopeFilter` → Handler

`BusServiceProvider` wires middleware instances from Domain and Application into the bus implementations.

## Domain Event Handler Logging

`HandleDomainEventJob` logs every event handler execution with structured context: `trace_id`, `level`, event class, handler class, tenant slug, and `duration_ms`. Logs `debug` before execution with event data (sensitive properties masked via `SensitiveDataMasker`), `info` on success, `error` on failure (with exception). The `failed()` callback also includes `trace_id` and `level` for permanent failures. Uses `TraceContext` contract for trace propagation.

## Auth Infrastructure

`AuthServiceProvider` binds auth-related domain contracts to Laravel implementations:

- `PasswordManager` → `EloquentPasswordManager` — hashes and stores passwords via `Hash::make()`
- `InviteLinkGenerator` → `LaravelInviteLinkGenerator` — generates signed invite URLs via `URL::temporarySignedRoute()` (72h expiry)
- `PasswordResetBroker` → `LaravelPasswordResetBroker` — wraps Laravel's `Password::broker()` for token creation/validation
- `TemplatedEmailDispatcher` → `DefaultTemplatedEmailDispatcher` — all system emails (invites, password reset, 2FA code, notifications) must render and send through email templates; direct `Mail::` sending is forbidden by architecture rule

## Scope Resolution

`CachedTeamMembershipChecker` is a request-scoped decorator (registered via `scoped()`) that memoizes `memberTeamIds()` and `visibleUserIds()` per userId within a single request. This prevents redundant DB calls when multiple scope-aware queries run in the same request.

## Logging

`App\Infrastructure\Logging\LaravelLogger` implements `App\Contract\Logging\Logger`, wrapping Laravel's `Log` facade. This is the only place in the application where `Log::` is called directly — all other code must inject the `Logger` interface. Enforced by PHPStan rule `NoDirectLoggingRule`.

## Repository + Mapper pattern

Repositories implement contract interfaces and use Eloquent models internally. Domain objects are never Eloquent models — mappers translate between Eloquent models and domain objects.

Tenant-level singleton policy repositories follow the same pattern as password rotation and 2FA settings (`EloquentPasswordRotationSettingsRepository`, `EloquentTwoFactorSettingsRepository`), while user-state adapters (`EloquentUserTwoFactorStateRepository`, `EloquentEmailTwoFactorChallengeRepository`) stay persistence-only and leave policy decisions to domain handlers.

`SimpleTwoFactorManager` issues TOTP secrets as RFC 4648 Base32 (160-bit random keys) and decodes them for HMAC-SHA1 per RFC 6238 so `otpauth://` QR codes work with Google Authenticator and other standard apps.

The `HasOptimisticLocking` trait (`app/Infrastructure/Eloquent/HasOptimisticLocking.php`) provides optimistic concurrency control. It overrides `performUpdate()` to add `WHERE version = ?` and increment the version column atomically. If 0 rows are affected (stale version), it throws `ConcurrentModificationException` (HTTP 409). The version is set to 1 on creation automatically.

The `PaginatesQuery` trait (`app/Infrastructure/Eloquent/PaginatesQuery.php`) provides a `paginateBuilder()` helper that Eloquent repositories use for paginated list methods. It executes a `COUNT(*)` + offset/limit query and returns `[list<Model>, int $total]` for the caller to map and wrap in `PaginatedResult`.

The `SortsQuery` trait (`app/Infrastructure/Eloquent/SortsQuery.php`) provides a `sortBuilder()` helper that applies `ORDER BY` to an Eloquent builder. Each repository using the trait must implement `textSortColumns()` returning column names where case-insensitive sorting via `LOWER()` applies. Non-text columns (timestamps, numerics) get plain `ORDER BY`. For computed columns like `permission_score`, repositories add `selectRaw` with the SQL expression before calling `sortBuilder()`.

The `FiltersQuery` trait (`app/Infrastructure/Eloquent/FiltersQuery.php`) provides a `filterBuilder()` helper that applies `WHERE` clauses to an Eloquent builder. Each repository using the trait must implement `searchColumns()` returning column names for fulltext search. Operators map to SQL: `Equals` → `WHERE col = ?`, `Contains` → `unaccent(col) ILIKE unaccent(?)`, `In` → `WHERE col IN (?)`, `Search` → OR-group across search columns. LIKE wildcards (`%`, `_`, `\`) are escaped via `escapeLike()` to prevent user input from being interpreted as patterns.

## File Storage

`App\Infrastructure\File\LaravelFileStorage` implements `App\Domain\File\Contract\FileStorage`, wrapping Laravel's `Illuminate\Contracts\Filesystem\Filesystem`. This is the only place in the application that touches the filesystem directly — all other code must inject the `FileStorage` interface. Storage paths use `{namespace}/{uuid}.{extension}` format. Files are streamed via `SplFileInfo::getPathname()` to avoid loading full contents into memory.

A dedicated `files` disk is configured in `config/filesystems.php`. Swapping storage backends (local ↔ S3) requires only changing `FILES_DISK_DRIVER` in `.env` — no code changes. The disk is resolved via `FilesystemFactory::disk('files')` in `RepositoryServiceProvider`.

Enforced by PHPStan rules `NoDirectFilesystemAccessRule` (bans facade calls and PHP file functions) and `NoDirectFilesystemImportRule` (bans framework filesystem imports) outside `Infrastructure\File\`.

## Tenant Schema Management

Schema-based multi-tenancy is implemented in `App\Infrastructure\Tenancy\`. See [Tenancy/README.md](Tenancy/README.md) for full details.

Key classes:
- `TenantSchemaManager` — switches `tenant` connection's `search_path` per request. Skips purge when connection details haven't changed.
- `TenantResolver` — resolves tenant by domain or slug via landlord DB
- `TenantBootstrapperImpl` — composes resolver + schema manager + context. Implements `TenantBootstrapper` contract so Presentation never imports Infrastructure directly.
- `ResolvedTenantContext` — mutable scoped singleton implementing `TenantContext`
- `TenantMigrator` — creates schema + runs tenant migrations (connection stays on the migrated tenant schema until `TenantBootstrapper::reset()` / `TenantSchemaManager::reset()`)
- `ConsoleTenantBootstrap` — event listener for CLI tenant resolution via `#[TenantAwareCommand]`

## Exception boundaries

Infrastructure must not throw Symfony HTTP exceptions (`HttpException`, `NotFoundHttpException`, etc.). When an infrastructure operation fails, throw a domain exception implementing `DomainException`. The Presentation layer catches domain exceptions and translates them to appropriate HTTP responses (e.g., `TenantNotFoundException` → 404 in `ResolveTenantMiddleware`).

Enforced by PHPStan rule `NoHttpExceptionsInInfrastructureRule` and PHPat rule `testInfrastructureDoesNotDependOnHttpExceptions`.

The Infrastructure layer may depend on Application, Domain, and Contract. It must not depend on Presentation.
