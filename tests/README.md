# Tests

## Directory structure

- `tests/Architecture/` — PHPat layer dependency tests (`ArchitectureTest.php`)
- `tests/Architecture/PHPStan/` — 9 custom PHPStan rules
- `tests/Unit/Domain/` — domain unit tests (100% coverage required)
- `tests/Unit/Infrastructure/` — infrastructure unit tests
- `tests/Feature/` — integration/feature tests
- `tests/Helper/` — shared test utilities

## Test rules

- **Unreachable code must not exist** — every line must be exercisable by tests. No `@codeCoverageIgnore` or coverage suppression of any kind.
- **100% coverage** — required across domain, infrastructure, and presentation layers. Enforced by `phpunit.coverage.xml` (unified, used by `check-and-fix`) and per-layer configs `phpunit.domain-coverage.xml`, `phpunit.infrastructure-coverage.xml`, `phpunit.presentation-coverage.xml` (used by `check` and for debugging which layer dropped).
- **No Mockery in domain tests** — `tests/Unit/Domain/` must not use `Mockery`. Use fake implementations instead. Enforced by `NoMockeryInDomainTestsRule`.

## Quality enforcement (PHPStan rules)

Custom rules in `tests/Architecture/PHPStan/`:

| Rule | Enforces |
|---|---|
| `NoCrossDomainDependenciesRule` | No `App\Domain\{A}` → `App\Domain\{B}` imports |
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

**No PHPStan baseline** — all errors must be fixed, not suppressed.

## Running tests

```
./vendor/bin/sail composer test
./vendor/bin/sail composer check-and-fix   # auto-fix then verify (parallel, unified coverage, vite build)
./vendor/bin/sail composer check           # full pipeline, check-only (CI, vite build)
./vendor/bin/sail composer check -- --frontend   # frontend only (lint, vitest, vite build)
./vendor/bin/sail composer check -- --backend    # backend only (pint, rector, phpstan, pest, coverage)
```
