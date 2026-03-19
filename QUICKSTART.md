# Quickstart

## Prerequisites

- Docker & Docker Compose

## Running commands

All PHP/Composer/Artisan commands **must** run through Laravel Sail. Never run them directly on the host.

```
./vendor/bin/sail composer check-and-fix
./vendor/bin/sail composer check
./vendor/bin/sail php artisan <command>
./vendor/bin/sail composer analyse
./vendor/bin/sail composer test
```

## Verification

For development, run:

```
./vendor/bin/sail composer check-and-fix
```

This auto-fixes first (biome, blade-formatter, rector, pint), then verifies (blade lint, vitest, vite build, pest with unified 100% coverage, phpstan). Frontend and backend run in parallel.

For CI (check-only, no modifications):

```
./vendor/bin/sail composer check
```

This runs in parallel waves: Wave 1 checks linting & static analysis (frontend: blade-formatter, blade lint, biome, vitest, vite build | PHP: pint, rector, phpstan). Wave 2 runs tests & per-layer coverage (pest, domain 100%, infrastructure 100%, presentation 100%).

All must pass. No warnings, no baselines, no ignores.

### Flags

Run only frontend or backend checks:

```
./vendor/bin/sail composer check -- --frontend
./vendor/bin/sail composer check -- --backend
./vendor/bin/sail composer check-and-fix -- --frontend
./vendor/bin/sail composer check-and-fix -- --backend
```

Override root path (useful for CI with non-standard working directories):

```
./vendor/bin/sail composer check -- --root /path/to/project
```

## Adding a new bounded context

1. Create domain directory: `app/Domain/{Context}/`
2. Add commands/queries with handlers — see [app/Domain/README.md](app/Domain/README.md) for CQRS walkthrough
3. Register handlers in `app/Infrastructure/Provider/BusServiceProvider.php` — see [app/Infrastructure/README.md](app/Infrastructure/README.md)
4. Add `#[RequiresPermission]` or `#[SkipPermissionCheck]` to every Command/Query — see [app/Domain/Authorization/README.md](app/Domain/Authorization/README.md)
5. Add controllers in `app/Presentation/Http/Controller/` — see [app/Presentation/README.md](app/Presentation/README.md)
6. Write tests, ensure 100% coverage across all layers

See [app/Domain/Organization/README.md](app/Domain/Organization/README.md) for a complete example of a bounded context with domain model, CRUD, cross-domain events, and multi-tenant membership.

## Customizing the template

- **App name**: Update `config/app.php`, `docker-compose.yml`, `.env`
- **Authorization modules**: Register in `config/authorization.php` — see [app/Domain/Authorization/README.md](app/Domain/Authorization/README.md)
- **Observability**: Configure OTel and Sentry env vars — see [docker/README.md](docker/README.md)
