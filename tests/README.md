# Tests

## Directory structure

- `tests/Architecture/` — PHPat layer dependency tests (`ArchitectureTest.php`)
- `tests/Architecture/PHPStan/` — custom PHPStan rules
- `tests/Unit/Domain/` — domain unit tests (100% coverage required)
- `tests/Unit/Infrastructure/` — infrastructure unit tests
- `tests/Feature/` — integration/feature tests
- `tests/Helper/` — shared test utilities

## Test rules

- **Unreachable code must not exist** — every line must be exercisable by tests. No `@codeCoverageIgnore` or coverage suppression of any kind.
- **100% coverage** — required across domain, infrastructure, and presentation layers. Enforced by `phpunit.coverage.xml` (unified, used by `check-and-fix`) and per-layer configs `phpunit.domain-coverage.xml`, `phpunit.infrastructure-coverage.xml`, `phpunit.presentation-coverage.xml` (used by `check` and for debugging which layer dropped).
- **No Mockery in domain tests** — `tests/Unit/Domain/` must not use `Mockery`. Use fake implementations instead. Enforced by `NoMockeryInDomainTestsRule`.
- **Transactional isolation** — all Feature tests use `TenantAwareRefreshDatabase` applied centrally in `Pest.php`. This trait creates landlord + tenant schemas once per suite and wraps each test in transactions on both connections. Individual test files must not import database traits directly. `LazilyRefreshDatabase`, `DatabaseMigrations`, and `DatabaseTransactions` are forbidden everywhere. Enforced by `NoDatabaseTraitsInTestsRule`.

## Quality enforcement (PHPStan rules)

Custom rules in `tests/Architecture/PHPStan/`:

| Rule | Enforces |
|---|---|
| `NoCrossDomainDependenciesRule` | No `App\Domain\{A}` → `App\Domain\{B}` imports (except `Domain\{B}\Contract\*` which is allowed from any domain) |
| `HandlersInDomainRule` | All `CommandHandler`, `QueryHandler`, `DomainEventHandler` implementations must live in `App\Domain\` |
| `NoMixedAnnotationsRule` | No `mixed` in `@param`, `@return`, `@var` PHPDoc in `App\` |
| `NoMockeryInDomainTestsRule` | No Mockery in `tests/Unit/Domain/` |
| `NoPhpstanIgnoreRule` | No `@phpstan-ignore` comments in `app/` and `tests/` |
| `NoStaticMethodDeclarationsInDomainRule` | No static method declarations in `App\Domain` |
| `NoStaticCallsInDomainRule` | No static calls in `App\Domain` (except `parent::`) |
| `OnlyDomainExceptionsInDomainRule` | Only `DomainException` implementors thrown in Domain |
| `UseStrictArgumentsInConsoleRule` | Console commands must use `StrictArguments` trait |
| `UseStrictRouteParametersRule` | Form requests must use `routeString()` not `route()` |
| `CommandQueryRequiresPermissionRule` | Every Command/Query must have `#[RequiresPermission]` or `#[SkipPermissionCheck]` |
| `ControllerRequiresPermissionRule` | Every Controller must have `#[RequiresPermission]` or `#[SkipPermissionCheck]` |
| `NoDatabaseTraitsInTestsRule` | No direct DB trait imports in tests; `RefreshDatabase` / `TenantAwareRefreshDatabase` only in `Pest.php` |
| `ControllerMustUseFormRequestRule` | Controllers must not type-hint `Illuminate\Http\Request` directly — use a custom `FormRequest` subclass |
| `ConsoleCommandRequiresTenantAttributeRule` | Every console command must have `#[TenantAwareCommand]` or `#[TenantAgnosticCommand]` |
| `NoScopeResolutionInPresentationRule` | Blocks `canWithScope()` calls in Presentation — scope resolution belongs in bus middleware |
| `NoSilentCatchRule` | Catch blocks in `app/` must rethrow, log, or carry a `// @silent: <reason>` comment |
| `NoDirectLoggingRule` | Bans `Log::`, `logger()`, `report()` in `app/` — use `Logger` interface instead |
| `NoMagicStringsRule` | No string literals in `===`/`!==` comparisons or `match()` arms — use enums or class constants |
| `NoMagicNumbersRule` | No numeric literals (except `0`, `1`, `-1`) in `app/` — use class constants |
| `EloquentModelRequiresTraitsRule` | Entity models must use `HasOptimisticLocking` and either `SoftDeletes` or `#[HardDelete(reason:)]` |
| `EventHandlerRequiresRetryPolicyRule` | Every `DomainEventHandler` must declare `#[RetryPolicy]` |
| `NoDirectFilesystemAccessRule` | Bans `Storage::`, `storage_path()`, PHP file functions (`fopen`, `unlink`, etc.) outside `Infrastructure\Filesystem\` |
| `NoDirectFilesystemImportRule` | Bans `Illuminate\Filesystem\*` and `Illuminate\Contracts\Filesystem\*` imports outside `Infrastructure\Filesystem\` |

**No PHPStan baseline** — all errors must be fixed, not suppressed.

## Adding a new PHPStan rule

1. Create the rule class in `tests/Architecture/PHPStan/`
2. Register in `phpstan.neon` as a service with `phpstan.rules.rule` tag
3. Run PHPStan to verify it reports expected violations
4. Fix all violations
5. Run full `composer check-and-fix`

PHPStan rules do not need dedicated unit tests — they are validated by running against the real codebase. The codebase itself is the test fixture.

## Running tests

```
./vendor/bin/sail composer test
./vendor/bin/sail composer check-and-fix   # auto-fix then verify (parallel, unified coverage, vite build)
./vendor/bin/sail composer check           # full pipeline, check-only (CI, vite build)
./vendor/bin/sail composer check -- --frontend   # frontend only (lint, vitest, vite build)
./vendor/bin/sail composer check -- --backend    # backend only (pint, rector, phpstan, pest, coverage)
```
