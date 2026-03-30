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

## Authorization attributes and interfaces

- `App\Application\Authorization\RequiresPermission` — attribute declaring the permission a command/query requires. Read by `AuthorizeAction` and `ResolveScopeFilter` bus middleware.
- `App\Application\Authorization\SkipPermissionCheck` — attribute for commands/queries that intentionally skip authorization.
- `App\Application\Authorization\ScopeAwareQuery` — interface for queries that receive automatic scope resolution via bus middleware. Extends `Query`. See [Domain README](../Domain/README.md) for usage.

The Application layer may depend on Domain and Contract. It must not depend on Infrastructure or Presentation.
