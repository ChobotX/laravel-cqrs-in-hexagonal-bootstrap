# Laravel CQRS Hexagonal Bootstrap

A Laravel starter built on CQRS and hexagonal architecture with 8 layers of automated enforcement.
It is made to be as strict as possible for consistent and maintainable AI development.

## Principles

- **KISS** — simplest solution that works, no premature abstraction
- **YAGNI** — don't build what isn't needed right now
- **DRY** — extract only when duplication is proven (3+ occurrences)
- **Radical Simplicity** — fewer files, fewer lines, fewer dependencies
- **SOLID** — single responsibility, open/closed, Liskov, interface segregation, dependency inversion
- **Stateless** — no sticky sessions, no per-pod file state, horizontally scalable on k8s

## Architecture

5-layer hexagonal structure under `app/`. Dependencies flow inward only.

| Layer | Namespace | May depend on | Must NOT depend on | Guide |
|---|---|---|---|---|
| **Contract** | `App\Contract` | nothing (pure interfaces) | any `App\*` namespace | [app/Contract/README.md](app/Contract/README.md) |
| **Domain** | `App\Domain` | Contract, Application | Infrastructure, Presentation | [app/Domain/README.md](app/Domain/README.md) |
| **Application** | `App\Application` | Domain, Contract | Infrastructure, Presentation | [app/Application/README.md](app/Application/README.md) |
| **Infrastructure** | `App\Infrastructure` | Application, Domain, Contract | Presentation | [app/Infrastructure/README.md](app/Infrastructure/README.md) |
| **Presentation** | `App\Presentation` | Application, Domain, Contract | Infrastructure | [app/Presentation/README.md](app/Presentation/README.md) |

Enforced by PHPat rules in `tests/Architecture/ArchitectureTest.php`.

## Multi-Tenancy

PostgreSQL schema-based isolation. Each tenant is a self-contained schema with zero cross-tenant data access.

- **Landlord schema** — minimal: `tenants` + `tenant_domains` tables only
- **Tenant schema** — everything else: users, teams, roles, permissions, jobs, cache
- **Per-tenant users** — like Slack workspaces. No shared user store. GDPR-compliant by architecture
- **Subdomain routing** — `tenant-a.laravel-bootstrap.local` resolves to `tenant_alpha` schema
- **Root domain** — landing page + tenant registration (no auth required)
- **Tenancy contracts in Domain** — `TenantContext`, `TenantBootstrapper`, `TenantLogoStorage`, and `DevSchemaResetter` live under `App\Domain\Tenancy\Contract\Service`. Non-tenancy domain contexts cannot import them (enforced by PHPat `testNonTenancyDomainDoesNotDependOnTenancy`)

See [app/Infrastructure/Tenancy/README.md](app/Infrastructure/Tenancy/README.md) for implementation details.

---

See [ADR.md](ADR.md) | [QUICKSTART.md](QUICKSTART.md) | [AGENTS.md](AGENTS.md) | [ROADMAP.md](ROADMAP.md)
