# Application Layer

Shared business logic layer. Contains bus interfaces, shared bus middleware, and cross-cutting orchestration that doesn't belong to a specific domain context.

## Bus interfaces

- `App\Application\Bus\CommandBus` — dispatches commands to domain handlers
- `App\Application\Bus\QueryBus` — dispatches queries to domain handlers
- `App\Application\Bus\EventBus` — dispatches domain events to async handlers

Usage from Presentation or Infrastructure:

```php
$this->commandBus->dispatch(new SomeCommand($data));
$result = $this->queryBus->dispatch(new SomeQuery($id));
```

## Shared Bus Middleware

Shared middleware in `App\Application\Bus\Middleware\` handles cross-cutting bus orchestration that doesn't belong to a specific domain context. Context-specific middleware (authorization) lives in the respective domain. Enforced by PHPStan rule `testMiddlewareDoesNotLiveInInfrastructure`.

- `LogBusMessage` — logs every command dispatch with structured context: `trace_id`, `user_id`, `tenant_id`, message class, `duration_ms`, and `level`. Logs `debug` before execution with message data (sensitive properties masked via `SensitiveDataMasker`), `info` on success, `error` on failure. Not applied to QueryBus — queries are read-only.
- `WrapInTransaction` — wraps handler execution in a database transaction via `TransactionManager` contract. Commands can opt out with `#[SkipTransaction(reason: '...')]`.
- `DispatchCollectedEvents` — runs the handler, then flushes collected events to the async queue via `EventBus`.

## Sensitive Data Masking

- `App\Application\Bus\Sensitive` — attribute marking a command/query/event property as sensitive. Properties annotated with `#[Sensitive]` are replaced with `'***'` in all bus log output. Use on passwords, tokens, and other secrets.
- `App\Application\Bus\SensitiveDataMasker` — static utility that reads `#[Sensitive]` attributes via reflection and masks annotated properties. Used by `LogBusMessage` middleware and `HandleDomainEventJob`.

## Transaction control

- `App\Application\Bus\SkipTransaction` — attribute opting a command out of the `WrapInTransaction` bus middleware. Requires a `reason` string. Use for commands that write to a different connection (e.g. landlord), run DDL/migrations, or manage their own transactions.

## Domain event control

- `App\Application\Bus\SkipDomainEvent` — attribute opting a command handler out of the `CommandHandlerMustCollectEventsRule` enforcement. Requires a `reason` string. Use for handlers that legitimately produce no domain events (infrastructure provisioning, data initialization).

## Property changes for update events

- `App\Application\Event\PropertyChange` — typesafe DTO representing a single field change in an update domain event. Properties: `property` (field name constant), `old` (previous value), `new` (new value), `sensitive` (boolean, defaults to false). For sensitive fields, use `PropertyChange::redacted($field)` (outside Domain) or `new PropertyChange($field, null, null, sensitive: true)` (inside Domain, where static calls are forbidden). Used by all `EntityUpdated` events.

## Authorization attributes and interfaces

- `App\Application\Authorization\RequiresPermission` — attribute declaring the permission a command/query requires. Read by `AuthorizeAction` and `ResolveScopeFilter` bus middleware.
- `App\Application\Authorization\SkipPermissionCheck` — attribute for commands/queries that intentionally skip authorization.
- `App\Application\Authorization\ScopeAwareQuery` — interface for queries that receive automatic scope resolution via bus middleware. Extends `Query`. See [Domain README](../Domain/README.md) for usage.

## Pagination

- `App\Application\Pagination\Pagination` — value object carrying `page` and `perPage` with clamping validation. Constants: `DEFAULT_PER_PAGE = 15`, `MAX_PER_PAGE = 100`.
- `App\Application\Pagination\PaginatedResult` — generic `@template T` container with `items`, `total`, `pagination`. Computed: `totalPages()`, `hasNextPage()`, `hasPreviousPage()`.
- `App\Application\Pagination\PaginableQuery` — interface for queries that carry pagination parameters. Follows the same `withX()/x()` pattern as `ScopeAwareQuery`.

## Sorting

- `App\Application\Sorting\SortDirection` — enum with `Asc` and `Desc` string-backed values. `toggle()` returns the opposite direction.
- `App\Application\Sorting\Sorting` — value object carrying `column` and `direction`. Constructor validates non-empty column.
- `App\Application\Sorting\SortableQuery` — interface for queries that carry sorting parameters. Follows the same `withX()/x()` pattern as `PaginableQuery` and `ScopeAwareQuery`.

The Application layer may depend on Domain and Contract. It must not depend on Infrastructure or Presentation.
