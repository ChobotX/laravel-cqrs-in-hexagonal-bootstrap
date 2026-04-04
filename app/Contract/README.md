# Contract Layer

Pure interfaces only. No classes, no traits, no abstract classes.

The Contract layer defines the boundaries of the hexagonal architecture. Every other layer may depend on Contract, but Contract must not depend on any `App\*` namespace.

## Contents

- `Bus/` — `Middleware` interface for bus middleware pipeline (`@template TResult` for type-safe return propagation through the pipeline)
- `Command/` — `Command`, `CommandHandler` interfaces
- `Event/` — `DomainEvent`, `DomainEventHandler`, `EventCollector`, `EntityDeleted` interfaces
- `Exception/` — `DomainException` interface (requires `userMessage(Translator): string` and `statusCode(): int`)
- `Http/` — `HttpStatus` interface with HTTP status code constants (`BAD_REQUEST`, `NOT_FOUND`, `FORBIDDEN`, `CONFLICT`, `UNPROCESSABLE_ENTITY`, `CREATED`, `NO_CONTENT`)
- `IdGenerator` — generates unique identifiers
- `Logging/` — `Logger` interface for framework-agnostic structured logging
- `Persistence/` — `TransactionManager` interface for database transaction control
- `Query/` — `Query`, `QueryHandler` interfaces
- `Tenancy/` — `TenantContext` (current tenant ID/slug), `TenantBootstrapper` (resolve + switch schema) — infrastructure ports, not domain contracts
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
