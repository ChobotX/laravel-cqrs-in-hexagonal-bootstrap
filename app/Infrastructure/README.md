# Infrastructure Layer

Concrete implementations of contracts and framework integrations. All classes must be `final`.

## BusServiceProvider registration

All command, query, and event handler mappings are registered in `app/Infrastructure/Provider/BusServiceProvider.php`.

```php
// Commands
{Name}Command::class => {Name}Handler::class,

// Queries
{Name}Query::class => {Name}Handler::class,

// Events
{EventClass}::class => [{HandlerClass}::class],
```

Every new command, query, or event handler must be registered here. See [Domain README](../Domain/README.md) for full CQRS walkthrough.

## Repository + Mapper pattern

Repositories implement contract interfaces and use Eloquent models internally. Domain objects are never Eloquent models — mappers translate between Eloquent models and domain objects.

The Infrastructure layer may depend on Application, Domain, and Contract. It must not depend on Presentation.
