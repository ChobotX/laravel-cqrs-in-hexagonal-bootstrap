# Architecture Decision Records

Enforced decisions with rationale. Not traditional numbered ADRs — fits the project's radical simplicity.

### Inward-only dependency flow (5 layers)

Contract ← Domain ← Application ← Infrastructure/Presentation. No layer may depend on a layer outside of it.
**Why:** Keeps domain logic free from framework and I/O concerns. Enables testing without infrastructure.
**Enforced by:** PHPat rules in `tests/Architecture/ArchitectureTest.php`.

### Handlers are domain logic, not orchestrators

Domain handlers contain business rules directly. They are not thin orchestrators delegating to services.
**Why:** Avoids unnecessary indirection. The hexagonal shell (Infrastructure + Presentation) handles I/O; handlers handle logic.
**Enforced by:** Convention. See [app/Domain/README.md](app/Domain/README.md).

### Stateless for k8s: cookie sessions, no file state, stderr logging

Auth via session guard with cookie driver (web) and Sanctum Bearer tokens (API). Sessions use cookie driver (client-side). Cache is array/database/redis. Logs go to stderr. No file-based state anywhere.
**Why:** Horizontal pod scaling without sticky sessions or shared filesystems.
**Enforced by:** Convention. Config removes file-based drivers.

### Stateless auth: session guard with cookie driver (web), Sanctum Bearer (API)

Web routes use Laravel's `session` auth guard with the `cookie` session driver (client-side, no server state). API routes use Sanctum Bearer tokens. Both mechanisms are fully stateless — no server-side session storage exists.
**Why:** The session guard with cookie driver provides standard Laravel auth flow (login/logout, CSRF) without server-side state. Sanctum Bearer tokens serve API consumers. Cookie session driver stores encrypted session data in the cookie itself, enabling horizontal pod scaling without sticky sessions.
**Enforced by:** `config/auth.php` (session guard), `config/session.php` (cookie driver), `routes/api.php` (auth:sanctum middleware).

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

### URL-based API versioning

Public API routes use URL-based versioning: `/api/v1/`. API controllers live under `App\Presentation\Http\Controller\Api\V1\{Context}\`. Internal API (`/internal-api/`) and web routes are unversioned. Resources and form requests remain unversioned until a breaking change requires a v2 variant.
**Why:** URL-based versioning is the simplest, most explicit approach. It makes the version visible in every request. Controller namespacing mirrors the URL structure and keeps v1 controllers frozen when v2 is introduced.
**Enforced by:** Route prefix in `routes/api.php`. Controller namespace convention.

### Single logging interface, no direct facade or helper usage

All logging must go through `App\Contract\Logging\Logger` (backend) or the `logger` module in `resources/js/logger/logger.ts` (frontend). Direct `Log::`, `logger()`, `report()` calls are banned in PHP. Direct `console.*` calls are banned in JS/TS. Catch blocks must either rethrow, log via the interface, or carry a `// @silent: <reason>` comment.
**Why:** Prevents silent error swallowing and ensures a single, injectable logging seam. One sanctioned path makes it easy to swap implementations (e.g. structured logging, Sentry breadcrumbs) without shotgun surgery.
**Enforced by:** PHPStan rules `NoSilentCatchRule` and `NoDirectLoggingRule`. Biome `noConsole` rule (frontend). Shell lint `bin/lint-catch-blocks.sh` (frontend catch blocks).

### Centralized transactional test isolation

All Feature tests use `RefreshDatabase` applied once in `Pest.php`. Individual test files must not import database traits directly. `LazilyRefreshDatabase`, `DatabaseMigrations`, and `DatabaseTransactions` are forbidden everywhere.
**Why:** Centralized config prevents accidental non-transactional traits that break parallel test execution and database isolation.
**Enforced by:** PHPStan rule `NoDatabaseTraitsInTestsRule`. See [tests/README.md](tests/README.md).

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
