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

The Application layer may depend on Domain and Contract. It must not depend on Infrastructure or Presentation.
