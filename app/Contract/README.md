# Contract Layer

Pure interfaces only. No classes, no traits, no abstract classes.

The Contract layer defines the boundaries of the hexagonal architecture. Every other layer may depend on Contract, but Contract must not depend on any `App\*` namespace.

## Contents

- `Auth/` — `AuthenticatedUser` (current user identity), `PasswordManager` (password operations)
- `Authorization/` — `AuthorizationChecker`, `AuthorizationRefresher`, `ImpersonationManager`, `AccessDecision`
- `Bus/` — `Middleware` interface for bus middleware pipeline
- `Command/` — `Command`, `CommandHandler` interfaces
- `Event/` — `DomainEvent`, `DomainEventHandler`, `EventCollector`, `EntityDeleted` interfaces
- `Logging/` — `Logger` interface for framework-agnostic structured logging
- `Notification/` — `NotificationBroadcaster`, `NotificationChannelSender`, `NotificationChannelSenderRegistry`, `RecipientResolver` interfaces for the notification system
- `Exception/` — `DomainException` interface (requires `userMessage(Translator): string` and `statusCode(): int`)
- `Http/` — `HttpStatus` interface with HTTP status code constants (`BAD_REQUEST`, `NOT_FOUND`, `FORBIDDEN`, `CONFLICT`, `UNPROCESSABLE_ENTITY`, `CREATED`, `NO_CONTENT`)
- `IdGenerator` — generates unique identifiers
- `Translation/` — `Translator` interface for framework-agnostic translations
- `Team/` — `TeamMembershipChecker`
- `Tenancy/` — `TenantContext` (current tenant ID/slug), `TenantBootstrapper` (resolve + switch schema), `TenantProvisioner` (create tenant + schema)
- `Query/` — `Query`, `QueryHandler` interfaces

## Generic type parameters

CQRS contracts use `@template` annotations for PHPStan type inference:

- `Query<TResult>` — declares the return type of the query
- `QueryHandler<TQuery, TResult>` — binds query type and return type
- `CommandHandler<TCommand>` — binds command type
- `DomainEventHandler<TEvent>` — binds event type

Concrete implementations must add `@implements` annotations (e.g. `@implements QueryHandler<GetUserByIdQuery, User>`) so PHPStan can resolve the concrete types inside handler method bodies and at dispatch call sites.
