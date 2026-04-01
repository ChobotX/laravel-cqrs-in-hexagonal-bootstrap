# Application Layer

Thin orchestration layer. Contains bus interfaces that dispatch commands, queries, and events to their domain handlers.

## Bus interfaces

- `App\Application\Bus\CommandBus` — dispatches commands to domain handlers
- `App\Application\Bus\QueryBus` — dispatches queries to domain handlers
- `App\Application\Bus\EventBus` — dispatches domain events to async handlers

Usage from Presentation or Infrastructure:

```php
$this->commandBus->dispatch(new SomeCommand($data));
$result = $this->queryBus->dispatch(new SomeQuery($id));
```

## Transaction control

- `App\Application\Bus\SkipTransaction` — attribute opting a command out of the `WrapInTransaction` bus middleware. Requires a `reason` string. Use for commands that write to a different connection (e.g. landlord), run DDL/migrations, or manage their own transactions.

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
