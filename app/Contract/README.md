# Contract Layer

Pure interfaces only. No classes, no traits, no abstract classes.

The Contract layer defines the boundaries of the hexagonal architecture. Every other layer may depend on Contract, but Contract must not depend on any `App\*` namespace.

## Contents

- `Auth/` — `PasswordManager` interface for password operations
- `Bus/` — `Middleware` interface for bus middleware pipeline
- `Command/` — `Command`, `CommandHandler` interfaces
- `Event/` — `DomainEvent`, `DomainEventHandler`, `EventCollector` interfaces
- `Exception/` — `DomainException` interface (requires `userMessage(Translator): string` and `statusCode(): int`)
- `Translation/` — `Translator` interface for framework-agnostic translations
- `Query/` — `Query`, `QueryHandler` interfaces

## Generic type parameters

CQRS contracts use `@template` annotations for PHPStan type inference:

- `Query<TResult>` — declares the return type of the query
- `QueryHandler<TQuery, TResult>` — binds query type and return type
- `CommandHandler<TCommand>` — binds command type
- `DomainEventHandler<TEvent>` — binds event type

Concrete implementations must add `@implements` annotations (e.g. `@implements QueryHandler<GetUserByIdQuery, User>`) so PHPStan can resolve the concrete types inside handler method bodies and at dispatch call sites.
