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

Auth via Sanctum tokens. Sessions use cookie driver (client-side). Cache is array/database/redis. Logs go to stderr. No file-based state anywhere.
**Why:** Horizontal pod scaling without sticky sessions or shared filesystems.
**Enforced by:** Convention. Config removes file-based drivers.

### Sanctum token auth, no session auth

Web routes use Sanctum token in HTTP-only cookie (middleware copies to Bearer header). API routes use standard Bearer header.
**Why:** Single auth mechanism for all routes. No server-side session state for auth.
**Enforced by:** Convention.

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

### No cross-domain imports

`App\Domain\{ContextA}` must not import from `App\Domain\{ContextB}`. Use QueryBus for cross-context data.
**Why:** Enforces bounded context isolation. Contexts communicate through the bus, not direct coupling.
**Enforced by:** PHPStan rule `NoCrossDomainDependenciesRule`. See [app/Domain/README.md](app/Domain/README.md).

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

### Centralized transactional test isolation

All Feature tests use `RefreshDatabase` applied once in `Pest.php`. Individual test files must not import database traits directly. `LazilyRefreshDatabase`, `DatabaseMigrations`, and `DatabaseTransactions` are forbidden everywhere.
**Why:** Centralized config prevents accidental non-transactional traits that break parallel test execution and database isolation.
**Enforced by:** PHPStan rule `NoDatabaseTraitsInTestsRule`. See [tests/README.md](tests/README.md).
