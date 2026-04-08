# Architecture Decision Records

Enforced decisions with rationale. Not traditional numbered ADRs — fits the project's radical simplicity.

### Inward-only dependency flow (5 layers)

Contract ← Domain ← Application ← Infrastructure/Presentation. No layer may depend on a layer outside of it.
**Why:** Keeps domain logic free from framework and I/O concerns. Enables testing without infrastructure.
**Enforced by:** PHPat rules in `tests/Architecture/ArchitectureTest.php`.

### Presentation imports only Domain Contracts

Presentation may only import from `App\Domain\{Module}\Contract\*`. Contract is the single public API surface of each domain module — it contains shared types (entities, enums, VOs), exceptions, events, commands, and queries. Everything else in Domain (handlers, services, internal VOs) is off-limits.
**Why:** Prevents Presentation from coupling to domain internals. One namespace to check, one boundary to enforce.
**Enforced by:** `testPresentationOnlyImportsDomainContracts` PHPat rule.

### Handlers are domain logic, not orchestrators

Domain handlers contain business rules directly. They are not thin orchestrators delegating to services.
**Why:** Avoids unnecessary indirection. The hexagonal shell (Infrastructure + Presentation) handles I/O; handlers handle logic.
**Enforced by:** Convention. See [app/Domain/README.md](app/Domain/README.md).

### Scalable for k8s: database sessions, no file state, stderr logging

Auth via session guard with database session driver (web) and Sanctum Bearer tokens (API). Sessions are stored in the tenant's `sessions` table (schema-isolated per tenant). Cache is array/database/redis. Logs go to stderr. No file-based state anywhere.
**Why:** Database sessions avoid the 4KB cookie size limit that caused silent validation error loss. Per-tenant schema isolation keeps sessions tenant-scoped. For k8s scaling, sticky sessions or a shared database are required (already the case with PostgreSQL).
**Enforced by:** Convention. Config removes file-based drivers. Session table in tenant migration.

### Database session auth: session guard with database driver (web), Sanctum Bearer (API)

Web routes use Laravel's `session` auth guard with the `database` session driver (server-side, per-tenant schema). API routes use Sanctum Bearer tokens. Sessions are stored in the `sessions` table within each tenant's PostgreSQL schema, providing automatic tenant isolation.
**Why:** The cookie session driver hit the 4KB browser cookie size limit, causing validation errors and flash data to be silently dropped. Database sessions have no size limit and integrate naturally with the schema-per-tenant architecture.
**Enforced by:** `config/auth.php` (session guard), `config/session.php` (database driver), `routes/api.php` (auth:sanctum middleware).

### No App→App inheritance

No class in `App\` may extend another `App\` class.
**Why:** Forces composition over inheritance across all layers.
**Enforced by:** PHPat rule. See [app/README.md](app/README.md).

### Domain classes final readonly

All domain classes must be `final readonly` (interfaces and throwables skip `readonly`).
**Why:** Immutable domain objects prevent unintended mutation and inheritance.
**Enforced by:** PHPat rule. See [app/Domain/README.md](app/Domain/README.md).

### Only DomainException in domain

Only `App\Contract\Exception\DomainException` implementors may be thrown in domain code.
**Why:** Domain exceptions carry user-facing messages and HTTP status codes by contract.
**Enforced by:** PHPStan rule `OnlyDomainExceptionsInDomainRule`. See [app/Domain/README.md](app/Domain/README.md).

### No cross-domain imports (with event exception)

`App\Domain\{ContextA}` must not import from `App\Domain\{ContextB}`. Use QueryBus for cross-context data. Exception: `EventHandler` classes may import `DomainEvent` classes from other domains — events are the intended cross-domain communication channel.
**Why:** Enforces bounded context isolation. Contexts communicate through the bus or domain events, not direct coupling.
**Enforced by:** PHPStan rule `NoCrossDomainDependenciesRule`. See [app/Domain/README.md](app/Domain/README.md).

### All handlers must live in Domain

All `CommandHandler`, `QueryHandler`, and `DomainEventHandler` implementations must live in `App\Domain\`. If a handler needs infrastructure (cache, broadcasting, mail), it must use a `Contract` interface — never import framework classes directly.
**Why:** Handlers contain business decisions (what/when). Moving them to Infrastructure to avoid domain purity rules is a backdoor that undermines hexagonal architecture. The decision is domain; the implementation is infrastructure.
**Enforced by:** PHPStan rule `HandlersInDomainRule`. See [app/Domain/README.md](app/Domain/README.md).

### Every Command/Query needs permission attribute

Every Command and Query must have `#[RequiresPermission]` or `#[SkipPermissionCheck]`.
**Why:** Forces a conscious authorization decision for every use case. No accidental unprotected endpoints.
**Enforced by:** PHPStan rule `CommandQueryRequiresPermissionRule`. See [app/Domain/Authorization/README.md](app/Domain/Authorization/README.md).

### Every controller needs permission attribute

Every controller in `App\Presentation\Http\Controller\` must have `#[RequiresPermission]` or `#[SkipPermissionCheck]`. A route middleware (`CheckPermission`) reads `#[RequiresPermission]` via reflection and enforces it before the controller executes.
**Why:** Eliminates duplicated boilerplate and forces a conscious authorization decision for every endpoint — same rationale as the Command/Query rule. Controller-level permission may differ from the underlying query (e.g. edit form needs `update` permission but renders via a `read` query).
**Enforced by:** PHPStan rule `ControllerRequiresPermissionRule` + middleware in `bootstrap/app.php`. See [app/Presentation/README.md](app/Presentation/README.md).

### Controllers must use FormRequest, never raw Request

Controllers must not type-hint `Illuminate\Http\Request` directly. If a controller needs request data, it must create a custom `FormRequest` subclass with validation rules and typed accessor methods.
**Why:** Raw `Request` bypasses validation and scatters input reading across controller bodies. `FormRequest` centralizes validation and provides typed accessors.
**Enforced by:** PHPStan rule `ControllerMustUseFormRequestRule`. See [app/Presentation/README.md](app/Presentation/README.md).

### 100% coverage, unreachable code must not exist

Domain, Infrastructure, and Presentation layers require 100% test coverage. Every line must be exercisable.
**Why:** Dead code and untested paths hide bugs. If code can't be tested, it must be refactored.
**Enforced by:** `phpunit.domain-coverage.xml`, `phpunit.infrastructure-coverage.xml`, `phpunit.presentation-coverage.xml`. See [tests/README.md](tests/README.md).

### No PHPStan baseline, no ignores, no coverage suppression

No `@phpstan-ignore`, no baseline file, no `@codeCoverageIgnore`.
**Why:** Suppression hides real issues. Every error and every line must be addressed.
**Enforced by:** PHPStan rule `NoPhpstanIgnoreRule`. See [tests/README.md](tests/README.md).

### No Mockery in domain tests

`tests/Unit/Domain/` must not use Mockery. Use fake implementations instead.
**Why:** Domain tests should validate real behavior, not mock contracts. Fakes catch more integration issues.
**Enforced by:** PHPStan rule `NoMockeryInDomainTestsRule`. See [tests/README.md](tests/README.md).

### Schema-based multi-tenancy

Each tenant gets a dedicated PostgreSQL schema. The domain layer is fully tenant-agnostic — it reads/writes "the database" without knowing which tenant is active. Infrastructure transparently routes queries to the correct schema via `search_path`.
**Why:** Enterprise-grade isolation. Per-tenant users, jobs, cache — zero cross-tenant data leakage by architecture. GDPR-compliant tenant deletion = `DROP SCHEMA CASCADE`.
**Enforced by:** PHPat rule `testDomainDoesNotDependOnTenancy` (domain cannot import `App\Contract\Tenancy`), PHPStan rule `ConsoleCommandRequiresTenantAttributeRule` (every console command must declare `#[TenantAwareCommand]` or `#[TenantAgnosticCommand]`).

### Every console command needs tenant attribute

Every command in `App\Presentation\Console\` must have `#[TenantAwareCommand]` or `#[TenantAgnosticCommand]`.
**Why:** Forces a conscious decision about whether each CLI command operates within a tenant schema or is tenant-agnostic (e.g. migration commands).
**Enforced by:** PHPStan rule `ConsoleCommandRequiresTenantAttributeRule`. See [tests/README.md](tests/README.md).

### Scope filtering happens in bus middleware, not controllers

Scope-based data filtering (All/Team/Own) is domain logic that must not leak into the Presentation layer. The `ResolveScopeFilter` bus middleware resolves the actor's scope transparently before the handler runs. Controllers dispatch queries and receive already-filtered results.
**Why:** Scope filtering was originally done in controllers — fetching all records and filtering in PHP. This violated hexagonal architecture (domain logic in presentation), harmed performance (full table loads), and duplicated logic across controllers.
**Enforced by:** PHPStan rule `NoScopeResolutionInPresentationRule` (blocks `canWithScope()` in Presentation), PHPat rules `testPresentationDoesNotDependOnTeamMembershipChecker` and `testPresentationDoesNotDependOnAccessContext` (block scope-related imports). See [app/Domain/Authorization/README.md](app/Domain/Authorization/README.md).

### Middleware lives in business layers, not Infrastructure

Bus middleware is business logic — the decision of *what to do* around handler execution (authorize, transact, log, dispatch events). Infrastructure only provides the framework implementations of contracts these middleware depend on. Context-specific middleware lives in its domain (`Domain\Authorization\Middleware\`), shared middleware lives in Application (`Application\Bus\Middleware\`).
**Why:** Middleware was originally in Infrastructure alongside framework plumbing. But the decisions it encodes (check permissions, wrap in transaction, log with trace context) are business rules. Putting them in Infrastructure violates the principle: "decision is domain, implementation is infra." Infrastructure dependencies (database, context facade) are abstracted behind contracts (`TransactionManager`, `TraceContext`).
**Enforced by:** PHPat rule `testMiddlewareDoesNotLiveInInfrastructure` in `tests/Architecture/ArchitectureTest.php`.

### URL-based API versioning

Public API routes use URL-based versioning: `/api/v1/`. API controllers live under `App\Presentation\Http\Controller\Api\V1\{Context}\`. Internal API (`/internal-api/`) and web routes are unversioned. Resources and form requests remain unversioned until a breaking change requires a v2 variant.
**Why:** URL-based versioning is the simplest, most explicit approach. It makes the version visible in every request. Controller namespacing mirrors the URL structure and keeps v1 controllers frozen when v2 is introduced.
**Enforced by:** Route prefix in `routes/api.php`. Controller namespace convention.

### Single logging interface, no direct facade or helper usage

All logging must go through `App\Contract\Logging\Logger` (backend) or the `logger` module in `resources/js/core/logger/logger.ts` (frontend). Direct `Log::`, `logger()`, `report()` calls are banned in PHP. Direct `console.*` calls are banned in JS/TS. Catch blocks must either rethrow, log via the interface, or carry a `// @silent: <reason>` comment.
**Why:** Prevents silent error swallowing and ensures a single, injectable logging seam. One sanctioned path makes it easy to swap implementations (e.g. structured logging, Sentry breadcrumbs) without shotgun surgery.
**Enforced by:** PHPStan rules `NoSilentCatchRule` and `NoDirectLoggingRule`. Biome `noConsole` rule (frontend). Shell lint `bin/lint-catch-blocks.sh` (frontend catch blocks).

### Centralized transactional test isolation

All Feature tests use `RefreshDatabase` applied once in `Pest.php`. Individual test files must not import database traits directly. `LazilyRefreshDatabase`, `DatabaseMigrations`, and `DatabaseTransactions` are forbidden everywhere.
**Why:** Centralized config prevents accidental non-transactional traits that break parallel test execution and database isolation.
**Enforced by:** PHPStan rule `NoDatabaseTraitsInTestsRule`. See [tests/README.md](tests/README.md).

### Every command handler must collect domain events

Every `CommandHandler` must inject `EventCollector` and fire at least one domain event. Handlers that legitimately produce no events (infrastructure provisioning, data initialization) must declare `#[SkipDomainEvent(reason: '...')]`.
**Why:** Domain events are the backbone of cross-domain communication, audit logging, and webhook delivery. Silent commands — those that mutate state without signaling — are invisible to the rest of the system. Requiring an explicit opt-out forces a conscious decision.
**Enforced by:** PHPStan rule `CommandHandlerMustCollectEventsRule`. See [app/Domain/README.md](app/Domain/README.md).

### Command bus transaction wrapper

All command handler execution (including event job insertion) is wrapped in a database transaction by the `WrapInTransaction` bus middleware. Nested `DB::transaction()` calls (from repositories or inner command dispatches) create PostgreSQL SAVEPOINTs. Commands can opt out with `#[SkipTransaction(reason: '...')]` (e.g. tenancy commands that write to the `landlord` connection or run DDL/migrations).
**Why:** Ensures atomicity -- handler writes and queued event jobs commit or roll back together. With the database queue driver, job rows are invisible to workers until commit.
**Enforced by:** Middleware pipeline order in `BusServiceProvider`. `#[SkipTransaction]` for opt-out.

### No magic literals

String literals in `===`/`!==` comparisons and `match()` arm conditions must use enums or class constants (empty string `''` excluded). Numeric literals other than `0`, `1`, `-1` must use class constants everywhere in `app/`. Constant definitions and enum case values are excluded.
**Why:** Magic literals bypass IDE navigation, refactoring safety, and exhaustiveness checking. Named constants make intent explicit and prevent typo-driven bugs.
**Enforced by:** PHPStan rules `NoMagicStringsRule` and `NoMagicNumbersRule`. See [tests/README.md](tests/README.md).

### Optimistic locking on all entity models

Every Eloquent entity model must use `HasOptimisticLocking`. Updates add `WHERE version = ?` and increment the version column atomically. Stale updates throw `ConcurrentModificationException` (HTTP 409). Junction models (identified by `$timestamps = false` or no primary key) are exempt. Models that don't need soft deletes must declare `#[HardDelete(reason: '...')]` with a mandatory explanation.
**Why:** Prevents silent data loss from concurrent updates in a multi-user, multi-tab, multi-device environment. The mandatory reason on `#[HardDelete]` forces a conscious decision about each model's deletion semantics.
**Enforced by:** PHPStan rule `EloquentModelRequiresTraitsRule`. See [tests/README.md](tests/README.md).

### Centralized file storage through domain contract

All file operations (store, retrieve, delete) must go through `App\Domain\File\Contract\FileStorage`. Direct filesystem access — `Storage::` facade, PHP file functions (`fopen`, `file_get_contents`, `unlink`, etc.), and `Illuminate\Filesystem` imports — is banned outside `App\Infrastructure\Filesystem\`. Files are organized by namespace (directory on disk), versioned in the database (never overwritten), and tracked with full metadata. The `FileUpload` domain value object wraps `\SplFileInfo` for framework-agnostic file input.
**Why:** Prevents scattered filesystem calls across the codebase, ensures every file has a database record (who uploaded, when, where), and makes swapping storage backends (local ↔ S3) a config change. Versioning prevents silent data loss.
**Enforced by:** PHPStan rules `NoDirectFilesystemAccessRule` and `NoDirectFilesystemImportRule`. See [app/Domain/File/README.md](app/Domain/File/README.md).

### Infrastructure must not throw HTTP exceptions

Infrastructure throws domain exceptions (implementing `DomainException`); the Presentation layer catches them and translates to HTTP responses. This prevents Infrastructure from making decisions about HTTP semantics.
**Why:** An `is_active` check in `TenantResolver` threw `NotFoundHttpException` — a business rule + HTTP decision in Infrastructure. Moving to domain exceptions keeps the concern boundary clean.
**Enforced by:** PHPStan rule `NoHttpExceptionsInInfrastructureRule`, PHPat rule `testInfrastructureDoesNotDependOnHttpExceptions`. See [tests/README.md](tests/README.md).

### Controllers must not orchestrate commands in loops

Controllers must not dispatch bus messages inside loops — neither commands nor queries. Command loops indicate orchestration that belongs in a domain handler. Query loops are N+1 performance problems that should use batch queries accepting multiple IDs.
**Why:** Sync logic for roles/teams/labels was duplicated across 3 controllers. Query loops dispatched per-user/per-team queries for list views. Both were replaced: sync logic moved to domain handlers (`SyncEntityLabelsCommand`, etc.), query loops replaced with batch queries (`GetRolesForUsersQuery`, `GetLabelsForEntitiesQuery`, `GetTeamsForUsersQuery`).
**Enforced by:** PHPStan rule `NoBusDispatchInControllerLoopsRule`. See [tests/README.md](tests/README.md).

### Blade templates must not reference non-Presentation App classes

Blade templates may only reference `App\Presentation\*` types. All other data (authenticated user, tenant slug, access scopes) must be passed from controllers or shared via middleware using `View::share`.
**Why:** Views referencing `App\Contract`, `App\Domain`, or `App\Application` types bypass the Presentation boundary. Service provider `View::share` in `AuthorizationServiceProvider` and middleware share common view data.
**Enforced by:** Shell lint `bin/lint-blade-layers.sh`. See [tests/README.md](tests/README.md).

### Frontend organized as core/behaviors/shared/widgets

The frontend uses an adapted Feature-Sliced Design with 4 categories: `core/` (infrastructure), `behaviors/` (vanilla DOM scripts), `shared/` (cross-cutting UI services), `widgets/` (feature-specific Vue micro-apps). Dependencies flow unidirectionally: `core ← shared ← widgets`, with `behaviors` isolated.
**Why:** Cross-widget imports were emerging organically (logger, toast, dialog used by many widgets). Formalizing the dependency hierarchy prevents coupling creep — same principle as backend layer rules. Adapted from FSD for our Blade+Vue micro-app architecture where all business logic is server-side.
**Enforced by:** dependency-cruiser rules in `.dependency-cruiser.cjs`, structural checks in `bin/lint-frontend-structure.sh`. See [app/Presentation/README.md](app/Presentation/README.md).

### Frontend boundary enforcement via dependency-cruiser

Import boundaries are validated by dependency-cruiser, a standalone CLI tool (no ESLint needed). It runs alongside Biome in CI, checking the full import graph for layer violations and cross-widget imports.
**Why:** Biome does not support import boundary rules. dependency-cruiser validates the import graph statically, catching cross-widget imports and layer direction violations before they merge. Parallels backend PHPat enforcement.
**Enforced by:** `npx depcruise` step in `bin/check.sh` and `bin/check-and-fix.sh`.

### Domain\*\Contract pattern for cross-domain contracts

Each bounded context exposes a `Contract` sub-namespace (`Domain/{Context}/Contract/`) containing types that may be imported cross-domain: value object IDs, repository interfaces, domain events, and service contracts. Internal types (handlers, exceptions, entities, non-ID value objects) are not importable cross-domain. The top-level `App\Contract` layer retains only truly shared infrastructure contracts (Command/Query/Event interfaces, Auth, Tenancy, etc.).
**Why:** Provides a formal, enforceable boundary between domain contexts. Cross-domain imports are limited to a stable, narrow API surface. Changes to internal domain types don't break other contexts.
**Enforced by:** PHPStan rule `NoCrossDomainDependenciesRule`. See [tests/README.md](tests/README.md).
