# Tests

## Directory structure

- `tests/Architecture/` — PHPat layer dependency tests (`ArchitectureTest.php`), including `testPresentationOnlyImportsDomainContracts` which enforces that Presentation only imports from `Domain\*\Contract` (the single public API surface of each module)
- `tests/Architecture/PHPStan/` — custom PHPStan rules
- `tests/Unit/Domain/` — domain unit tests (100% coverage required)
- `tests/Unit/Infrastructure/` — infrastructure unit tests
- `tests/Feature/` — integration/feature tests
- `tests/Helper/` — shared test utilities
- `tests/e2e/` — Playwright end-to-end tests (browser-based, against running app)

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
| `CommandHandlerMustCollectEventsRule` | Every `CommandHandler` must inject `EventCollector` or declare `#[SkipDomainEvent(reason:)]` |
| `UpdatedEventMustImplementEntityUpdatedRule` | Every event class ending in "Updated" in `Contract/Event/` must implement `EntityUpdated` |
| `NoDirectFilesystemAccessRule` | Bans `Storage::`, `storage_path()`, PHP file functions (`fopen`, `unlink`, etc.) outside `Infrastructure\Filesystem\` |
| `NoDirectFilesystemImportRule` | Bans `Illuminate\Filesystem\*` and `Illuminate\Contracts\Filesystem\*` imports outside `Infrastructure\Filesystem\` |
| `FileStorageOnlyInFileDomainRule` | Bans `FileStorage` contract usage outside `Domain\File` and `Infrastructure\Filesystem` — forces bus usage |
| `ControllerDependenciesRule` | Controllers may only inject `CommandBus`, `QueryBus`, `AuthenticatedUser`, `AuthorizationChecker`, `IdGenerator`, `Guard` — no domain services or infrastructure |
| `NoHttpExceptionsInInfrastructureRule` | Infrastructure must not throw Symfony HTTP exceptions — throw domain exceptions instead, Presentation translates |
| `NoBusDispatchInControllerLoopsRule` | Controllers must not call `->dispatch()` inside foreach loops — use batch queries or aggregate commands instead |
| `NoLooseFilesInDomainModuleRule` | No PHP classes directly at `Domain/{Module}/` root — must be in a typed subdirectory (`Handler/`, `ValueObject/`, `Enum/`, etc.) |
| `NoLooseFilesInContractRule` | No PHP classes directly at `Domain/{Module}/Contract/` root — must be in a typed subdirectory (`Entity/`, `ValueObject/`, `Repository/`, etc.) |
| `ContractSubdirectoryTypeEnforcementRule` | Enforces type constraints per Contract subdirectory: `Repository/`/`Service/` → interface, `Enum/` → enum, `Command/` → implements Command, etc. |
| `DomainSubdirectoryTypeEnforcementRule` | Enforces type constraints per Domain subdirectory: `Enum/` → enum, `Handler/Command/` → implements CommandHandler, `EventHandler/` → implements DomainEventHandler, etc. Exempt: `Registry/Schema/` |
| `NoDomainSpecificContractsInGlobalContractRule` | `App\Contract\` may only contain framework-level namespaces (Bus, Command, Query, Event, etc.) — domain-specific contracts must live in `Domain/{Module}/Contract/` |

**No PHPStan baseline** — all errors must be fixed, not suppressed.

## Blade lint scripts

Shell scripts in `bin/` enforce Blade template rules:

| Script | Enforces |
|---|---|
| `lint-blade-no-js.sh` | No inline `<script>` tags, event handlers, or `javascript:` URLs |
| `lint-blade-a11y.sh` | Accessibility checks on Blade templates |
| `lint-blade-layers.sh` | Blade templates must not reference `App\*` namespaces except `App\Presentation\*` — all data via controllers/middleware |

## Frontend architecture enforcement

Import boundaries are validated by dependency-cruiser (`.dependency-cruiser.cjs`), structural conventions by `bin/lint-frontend-structure.sh`.

### dependency-cruiser rules

| Rule | Enforces |
|---|---|
| `no-core-upward-deps` | `core/` must not import from `shared/`, `behaviors/`, or `widgets/` |
| `no-behaviors-upward-deps` | `behaviors/` must not import from `shared/` or `widgets/` |
| `no-shared-upward-deps` | `shared/` must not import from `widgets/` or `behaviors/` |
| `no-cross-widget-imports` | `widgets/X/` must not import from `widgets/Y/` |
| `no-vue-in-core-or-behaviors` | `core/` and `behaviors/` must not import Vue |
| `no-circular` | No circular dependencies |

### Structure lint rules (`bin/lint-frontend-structure.sh`)

| Rule | Enforces |
|---|---|
| PascalCase Vue files | `.vue` filenames must start with uppercase |
| Correct TS casing | kebab-case for regular files, camelCase for `use*` composables, PascalCase for component test files |
| No barrel files | No `index.ts` files (complements Biome `noBarrelFile`) |
| createApp isolation | Only `*-app.ts` files may call `createApp()` |
| Widget bootstrapper | Widget dirs with Vue files must have `*-app.ts` |
| No loose root files | Only `app.ts` allowed at `resources/js/` root |

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

## E2E tests (Playwright)

Browser-based tests that verify user-facing flows against a running Sail instance. Separate from `composer check` — require a running app with seeded data and a host-side browser.

```
npm run test:e2e          # headless
npm run test:e2e:ui       # interactive UI mode
```

See [tests/e2e/README.md](e2e/README.md) for setup, best practices, and auth patterns.

`data-testid` attributes are the project convention for e2e selectors. Blade components use the `testId` prop; Vue components use `data-testid` directly.
