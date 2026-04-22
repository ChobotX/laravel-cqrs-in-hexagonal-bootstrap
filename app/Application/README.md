# Application Layer

Shared cross-cutting primitives used by Domain handlers: bus middleware, value objects (pagination, sorting, filtering, access context, property changes), scope-resolution markers, and utilities. Bus ports and attributes are part of the public Contract surface and live under `App\Contract\` (see [Contract README](../Contract/README.md)).

## Bus interfaces (in Contract)

- `App\Contract\Bus\CommandBus` — dispatches commands to domain handlers
- `App\Contract\Bus\QueryBus` — dispatches queries to domain handlers
- `App\Contract\Bus\EventBus` — dispatches domain events to async handlers

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

- `App\Contract\Attribute\Sensitive` — attribute marking a command/query/event property as sensitive. Properties annotated with `#[Sensitive]` are replaced with `'***'` in all bus log output. Use on passwords, tokens, and other secrets.
- `App\Application\Bus\SensitiveDataMasker` — static utility that reads `#[Sensitive]` attributes via reflection and masks annotated properties. Used by `LogBusMessage` middleware and `HandleDomainEventJob`.

## Transaction control

- `App\Contract\Attribute\SkipTransaction` — attribute opting a command out of the `WrapInTransaction` bus middleware. Requires a `reason` string. Use for commands that write to a different connection (e.g. landlord), run DDL/migrations, or manage their own transactions.

## Domain event control

- `App\Contract\Attribute\SkipDomainEvent` — attribute opting a command handler out of the `CommandHandlerMustCollectEventsRule` enforcement. Requires a `reason` string. Use for handlers that legitimately produce no domain events (infrastructure provisioning, data initialization).

## Property changes for update events

- `App\Application\Event\PropertyChange` — typesafe DTO representing a single field change in an update domain event. Properties: `property` (field name constant), `old` (previous value), `new` (new value), `sensitive` (boolean, defaults to false). For sensitive fields, use `PropertyChange::redacted($field)` (outside Domain) or `new PropertyChange($field, null, null, sensitive: true)` (inside Domain, where static calls are forbidden). Used by all `EntityUpdated` events.

## Authorization attributes and interfaces

- `App\Contract\Attribute\RequiresPermission` — attribute declaring the permission a command/query requires. Read by `AuthorizeAction` and `ResolveScopeFilter` bus middleware.
- `App\Contract\Attribute\SkipPermissionCheck` — attribute for commands/queries that intentionally skip authorization.
- `App\Application\Authorization\ScopeAwareQuery` — interface for queries that receive automatic scope resolution via bus middleware. Extends `Query`. See [Domain README](../Domain/README.md) for usage.
- `App\Application\Authorization\ShareableScopeQuery` — opt-in marker interface (complements `ScopeAwareQuery`). Queries that declare a `shareableResourceType()` automatically have shared resource IDs resolved into `AccessContext::$sharedResourceIds` by `ResolveScopeFilter`.
- `App\Application\Authorization\ShareableResourceRegistry` — maps `resource_type` to its permission prefix (e.g. `'entry' => 'registry.entries'`). Used by `AuthorizationChecker::canShareResource/canViewResourceShares` so generic share endpoints resolve the right permissions without knowing about specific entity types. Bound in `AuthorizationServiceProvider`.

## Pagination

- `App\Application\Pagination\Pagination` — value object carrying `page` and `perPage` with clamping validation. Constants: `DEFAULT_PER_PAGE = 15`, `MAX_PER_PAGE = 100`.
- `App\Application\Pagination\PaginatedResult` — generic `@template T` container with `items`, `total`, `pagination`. Computed: `totalPages()`, `hasNextPage()`, `hasPreviousPage()`.
- `App\Application\Pagination\PaginableQuery` — interface for queries that carry pagination parameters. Follows the same `withX()/x()` pattern as `ScopeAwareQuery`.

## Sorting

- `App\Application\Sorting\SortDirection` — enum with `Asc` and `Desc` string-backed values. `toggle()` returns the opposite direction.
- `App\Application\Sorting\Sorting` — value object carrying `column` and `direction`. Constructor validates non-empty column.
- `App\Application\Sorting\SortableQuery` — interface for queries that carry sorting parameters. Follows the same `withX()/x()` pattern as `PaginableQuery` and `ScopeAwareQuery`.

The Application layer may depend on Domain and Contract. It must not depend on Infrastructure or Presentation.
