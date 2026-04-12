# Agent Navigation

Laravel CQRS hexagonal bootstrap — a stateless, strictly enforced 5-layer architecture with 100% coverage.

## Documentation map

| File | Topic |
|---|---|
| [README.md](README.md) | Project overview, principles, architecture table |
| [ADR.md](ADR.md) | Enforced architecture decisions with rationale |
| [QUICKSTART.md](QUICKSTART.md) | Setup, commands, verification, adding contexts |
| [app/README.md](app/README.md) | Cross-layer class rules, code style conventions |
| [app/Contract/README.md](app/Contract/README.md) | Contract layer — pure interfaces, generic types |
| [app/Domain/README.md](app/Domain/README.md) | Domain layer — handlers, value objects, CQRS patterns, events |
| [app/Application/README.md](app/Application/README.md) | Application layer — bus interfaces |
| [app/Infrastructure/README.md](app/Infrastructure/README.md) | Infrastructure — handler registration, repository pattern |
| [app/Presentation/README.md](app/Presentation/README.md) | Presentation — controllers, form requests, console, views, Vue |
| [app/Domain/Authorization/README.md](app/Domain/Authorization/README.md) | Authorization module — permissions, RBAC, impersonation |
| [app/Domain/Team/README.md](app/Domain/Team/README.md) | Team module — hierarchy, membership, scope filtering |
| [app/Domain/Label/README.md](app/Domain/Label/README.md) | Label module — universal namespaced labels, polymorphic assignment, orphan cleanup |
| [app/Domain/File/README.md](app/Domain/File/README.md) | File module — centralized storage, namespace directories, versioning, PHPStan enforcement |
| [app/Domain/Notification/README.md](app/Domain/Notification/README.md) | Notification module — multi-channel delivery, preferences, real-time |
| [app/Domain/Registry/README.md](app/Domain/Registry/README.md) | Registry module — dynamic schema-driven CRUD, versioned definitions, typed entries |
| [app/Domain/FeatureFlag/README.md](app/Domain/FeatureFlag/README.md) | Feature flag module — code-defined flags, per-tenant overrides, boolean/select/input types |
| [app/Domain/GridPreset/README.md](app/Domain/GridPreset/README.md) | Grid preset module — saved filter/sort/search presets per user, default views |
| [app/Infrastructure/Tenancy/README.md](app/Infrastructure/Tenancy/README.md) | Tenancy — schema isolation, tenant resolution, migration |
| [tests/README.md](tests/README.md) | Testing strategy, PHPStan rules, coverage requirements |
| [tests/e2e/README.md](tests/e2e/README.md) | E2E testing — Playwright setup, auth pattern, best practices |
| [docker/README.md](docker/README.md) | Observability — OpenTelemetry, Sentry, production Dockerfile |
| [ROADMAP.md](ROADMAP.md) | Feature roadmap and planned work |

## Critical rules before touching code

1. **Run via Sail** — `./vendor/bin/sail composer check-and-fix` for development (auto-fixes then verifies). `./vendor/bin/sail composer check` for CI (check-only, no modifications). Append `-- --frontend` or `-- --backend` to run only one side. Never run PHP on host. In Claude Code sessions, prefer the `/qa` skill for smart targeted checks on changed files only — it auto-detects what changed and runs the minimum required checks.
2. **`composer check` must pass** — linting & static analysis (pint, blade-formatter, blade lint, biome, rector, phpstan, vite build) then tests & per-layer 100% coverage, all in parallel waves. No warnings, no baselines, no ignores.
3. **Permission attributes required** — every Command/Query and every Controller needs `#[RequiresPermission]` or `#[SkipPermissionCheck]`.
4. **No cross-domain imports** — `App\Domain\{A}` must not import from `App\Domain\{B}`. Use QueryBus.
5. **100% coverage** — domain, infrastructure, and presentation layers. No `@codeCoverageIgnore`.
6. **No PHPStan suppression** — no `@phpstan-ignore`, no baseline.
7. **No Mockery in domain tests** — use fakes.

## Keeping documentation consistent

Every code change that alters rules, patterns, or architecture **must** update all affected docs in the same commit. Information is intentionally duplicated across files for discoverability — inconsistency is a bug.

### Cross-reference map

When you change **one** of these, check **all** listed files:

| Change | Files to update |
|---|---|
| Add/remove PHPStan rule | `tests/README.md` (rule table), relevant layer README (e.g. `app/Domain/README.md`), `ADR.md` if it enforces an architecture decision |
| Add/change layer dependency rule | `README.md` (architecture table), `ADR.md`, affected layer READMEs |
| Add/change CQRS pattern | `app/Domain/README.md`, `app/Infrastructure/README.md` (registration), `QUICKSTART.md` (adding a context) |
| Add/change authorization module/feature | `app/Domain/Authorization/README.md`, `config/authorization.php`, `QUICKSTART.md` |
| Add/change label module/feature | `app/Domain/Label/README.md`, `config/authorization.php` |
| Add/change shareable resource type or sharing mechanism | `app/Domain/Authorization/README.md` (Record Sharing section), `app/Infrastructure/Provider/AuthorizationServiceProvider.php` (`ShareableResourceRegistry` binding), owning entity's domain README, `ADR.md` if pattern changes |
| Add/change code style rule | `app/README.md`, `pint.json` or `rector.php` (whichever enforces it) |
| Add/change class structural rule | `app/README.md` (class rules table), `ADR.md` if it's a new decision |
| Add/change presentation pattern | `app/Presentation/README.md` |
| Add/change frontend architecture rule | `app/Presentation/README.md`, `.dependency-cruiser.cjs`, `bin/lint-frontend-structure.sh`, `ADR.md` |
| Move/add frontend module | `app/Presentation/README.md` (architecture table), `.dependency-cruiser.cjs` if new category |
| Add/change API versioning pattern | `ADR.md`, `app/Presentation/README.md`, `routes/api.php`, `QUICKSTART.md` |
| Add/change file storage pattern | `app/Domain/File/README.md`, `app/Infrastructure/README.md`, `ADR.md` |
| Add/change registry module/feature | `app/Domain/Registry/README.md`, `config/authorization.php` |
| Add/change feature flag module/feature | `app/Domain/FeatureFlag/README.md`, `config/authorization.php`, `config/feature-flags.php` |
| Add/change infrastructure pattern | `app/Infrastructure/README.md` |
| Add/change contract interface | `app/Contract/README.md` (contents list + generic type docs if applicable) |
| Add/change coverage config | `tests/README.md`, `AGENTS.md` (coverage config row) |
| Add/change e2e test pattern | `tests/e2e/README.md`, `tests/README.md`, `playwright.config.ts` |
| Add/change observability config | `docker/README.md` |
| Add/remove a documentation file | `AGENTS.md` (documentation map table) |

### Rules

1. **Same commit** — doc updates go in the same commit as the code change, not as a follow-up.
2. **Check the map above** — before committing, scan the cross-reference map for every category your change touches.
3. **ADR.md gets a new entry** when the change introduces a new enforced architecture decision (not for minor tweaks to existing ones).
4. **AGENTS.md documentation map** must list every `README.md` and top-level doc. If you create a new doc, add it here.
5. **No stale examples** — if a code pattern changes, update all code snippets that demonstrate it across all docs.
6. **Verify links** — when renaming or moving files, update all `[text](path)` references across all docs.
7. **Isolated atomic commits** — each commit must be self-contained and independently valid. Do not mix unrelated changes. One logical change (feature, fix, refactor) = one commit with its docs.

## Key files for common tasks

| Task | Key files |
|---|---|
| Add a command/query | [app/Domain/README.md](app/Domain/README.md), `app/Infrastructure/Provider/BusServiceProvider.php` |
| Add authorization | [app/Domain/Authorization/README.md](app/Domain/Authorization/README.md), `config/authorization.php` |
| Add a controller | [app/Presentation/README.md](app/Presentation/README.md) |
| Architecture tests (backend) | `tests/Architecture/ArchitectureTest.php` |
| Architecture rules (frontend) | `.dependency-cruiser.cjs`, `bin/lint-frontend-structure.sh` |
| PHPStan custom rules | `tests/Architecture/PHPStan/` |
| Code style config | `pint.json`, `rector.php` |
| Coverage config | `phpunit.coverage.xml` (unified), `phpunit.domain-coverage.xml`, `phpunit.infrastructure-coverage.xml`, `phpunit.presentation-coverage.xml` (per-layer) |
| Add an e2e test | [tests/e2e/README.md](tests/e2e/README.md), `playwright.config.ts` |
