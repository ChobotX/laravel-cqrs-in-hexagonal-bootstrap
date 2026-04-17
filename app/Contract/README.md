# Contract Layer

Pure interfaces only. No classes, no traits, no abstract classes.

The Contract layer defines the boundaries of the hexagonal architecture. Every other layer may depend on Contract, but Contract must not depend on any `App\*` namespace.

## Contents

- `Bus/` — `Middleware` interface for bus middleware pipeline (`@template TResult` for type-safe return propagation through the pipeline)
- `Command/` — `Command`, `CommandHandler` interfaces
- `Event/` — `DomainEvent`, `DomainEventHandler`, `EventCollector`, `EntityDeleted` interfaces (structured “updated” payloads use `App\Application\Event\EntityUpdated` in domain modules — see [Domain README](../Domain/README.md))
- `Exception/` — `DomainException` interface (requires `userMessage(Translator): string` and `statusCode(): int`)
- `Http/` — `HttpStatus` interface with HTTP status code constants (`BAD_REQUEST`, `NOT_FOUND`, `FORBIDDEN`, `CONFLICT`, `UNPROCESSABLE_ENTITY`, `CREATED`, `NO_CONTENT`)
- `IdGenerator` — generates unique identifiers
- `Logging/` — `Logger` interface for framework-agnostic structured logging
- `Persistence/` — `TransactionManager` interface for database transaction control
- `Query/` — `Query`, `QueryHandler` interfaces
- `Tenancy/` — `TenantContext` (current tenant ID/slug/name/logo URL), `TenantBootstrapper` (resolve + switch schema), `TenantLogoStorage` — infrastructure ports, not domain contracts
- `Tracing/` — `TraceContext` interface for request context propagation (`traceId`, `userId`, `tenantId`)
- `Translation/` — `Translator` interface for framework-agnostic translations

Domain-specific contracts (auth, authorization, notifications, file processing, registry validation) live in their respective `Domain/{Context}/Contract/Service/` namespaces.

## Generic type parameters

CQRS contracts use `@template` annotations for PHPStan type inference:

- `Query<TResult>` — declares the return type of the query
- `QueryHandler<TQuery, TResult>` — binds query type and return type
- `CommandHandler<TCommand>` — binds command type
- `DomainEventHandler<TEvent>` — binds event type

Concrete implementations must add `@implements` annotations (e.g. `@implements QueryHandler<GetUserByIdQuery, User>`) so PHPStan can resolve the concrete types inside handler method bodies and at dispatch call sites.

## Documentation

Types under `App\Contract` and `App\Domain\{Context}\Contract` are the **public API** for other layers. They must carry **intent** documentation: when to use the type, semantics consumers rely on, and meaning of each property (not a restatement of native types).

Rules (aligned with Pint `no_empty_phpdoc` / `no_superfluous_phpdoc_tags`):

- **Interfaces** — Non-empty class docblock (purpose, who implements or calls). Per-method prose when behavior is non-obvious. Keep `@template` / `@param TCommand`-style tags only where PHPStan needs them; do not add redundant `@param` / `@return` that duplicate declared types.
- **Readonly DTOs** (commands, queries, events, entities, value objects in domain `Contract/`) — Class-level docblock describing the type in context. For promoted constructor parameters, place a **docblock immediately before each parameter** so IDEs and PHPStan attach description to the property; describe meaning (e.g. identifier format, what `null` means), not the PHP type.
- **Enums** — Class-level docblock; per-case comments only when the case name is insufficient.

### Examples

**Global port (interface):**

```php
/**
 * Runs after the inner bus dispatch; may short-circuit, transform the result, or observe side effects.
 *
 * @template TResult
 */
interface BusMiddleware
{
    // ...
}
```

**Domain query (readonly class + generics):**

```php
/**
 * Loads a single user by primary key for detail views and authorization checks.
 *
 * @implements Query<User>
 */
#[RequiresPermission('users.list.read')]
final readonly class GetUserByIdQuery implements Query
{
    public function __construct(
        /** Primary user identifier (UUID). */
        public string $id,
    ) {}
}
```

**Domain entity (readonly data carrier):**

```php
/**
 * Snapshot of a user row as exposed to other contexts through queries (not a mutable aggregate).
 */
final readonly class User
{
    public function __construct(
        /** Stable user id used across commands and queries. */
        public UserId $id,
        // ...
    ) {}
}
```

Domain-specific contracts also live under `Domain/{Context}/Contract/` — the same documentation rules apply; see [app/README.md](../app/README.md) (contract paths) and [app/Domain/README.md](../Domain/README.md) (CQRS workflow).
