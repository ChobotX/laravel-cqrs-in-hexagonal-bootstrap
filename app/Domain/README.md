# Domain Layer

Core business logic. All classes must be `final readonly` (interfaces and throwables skip `readonly`).

## Handlers as Domain Orchestrators

In this architecture, handlers ARE the domain-level orchestrators. The `User` aggregate is intentionally a data carrier — a "Transaction Script within Hexagonal Shell" pattern. Events are raised in handlers, not in the aggregate itself.

The key invariant enforced: nothing mutates users outside the handler flow. All user operations must go through the CQRS bus pipeline.

Handlers live in `App\Domain` (not `App\Application`) because in this architecture, Domain = business logic + use cases. `App\Application` holds cross-cutting primitives (shared bus middleware, pagination/sorting/filtering VOs, scope markers). Bus ports (`CommandBus`/`QueryBus`/`EventBus`) and attributes (`#[RequiresPermission]`, `#[Sensitive]`, etc.) are part of the public Contract surface under `App\Contract`. No extra indirection layer is needed between Presentation and Domain.

Domain services stay narrow and reusable. They should not replace handlers as workflow coordinators; sequencing across multiple steps belongs in handlers.

## Value Objects

Use value objects for values with validation rules, equality semantics, or that appear in multiple places.

- `Email` — validates format via `filter_var(FILTER_VALIDATE_EMAIL)`, normalizes to lowercase, implements `equals()` and `Stringable`
- `UserId` — validates UUID format via regex
- `UserName` — validates non-empty trimmed string, implements `Stringable`

## Domain rules

Custom PHPStan rules in `tests/Architecture/PHPStan/`.

- **No static method declarations** in `App\Domain` (`NoStaticMethodDeclarationsInDomainRule`) — this means domain exceptions must use public constructors (like `new UserNotFoundException($id)`), NOT static factory methods (like `UserNotFoundException::forId($id)`). The `parent::__construct()` call is the only allowed static call.
- **No static calls** in `App\Domain` except `parent::` (`NoStaticCallsInDomainRule`)
- **Only `App\Contract\Exception\DomainException` implementors** can be thrown — the interface requires a `userMessage(Translator): string` method and a `statusCode(): int` method (`OnlyDomainExceptionsInDomainRule`)
- **No Laravel helpers** in `App\Domain` — `__()`, `app()`, `config()`, etc. are blocked; use Contract interfaces instead (`NoLaravelHelpersInDomainRule`)
- **No cross-domain dependencies** — `App\Domain\{ContextA}` must not depend on `App\Domain\{ContextB}` except via `Domain\{ContextB}\Contract\*` (`DomainCrossDomainImportsRule`, `DomainCrossDomainGroupUseImportsRule`)
- **No loose files in module roots** — every PHP class in `Domain/{Module}/` must live in a typed subdirectory (`Handler/`, `ValueObject/`, `Enum/`, `Service/`, `Policy/`, `Constant/`, `Exception/`, `EventHandler/`, `Middleware/`, `Schema/`), not at the module root (`NoLooseFilesInDomainModuleRule`)
- **No loose files in Contract roots** — every PHP class in `Domain/{Module}/Contract/` must live in a typed subdirectory (`Entity/`, `ValueObject/`, `Repository/`, `Service/`, `Enum/`, `Command/`, `Query/`, `Event/`, `Exception/`), not at the Contract root (`NoLooseFilesInContractRule`)
- **Contract subdirectory types enforced** — `Repository/` and `Service/` must contain interfaces, `Enum/` must contain enums, `Command/`/`Query/`/`Event/`/`Exception/` must implement their respective Contract interfaces (`ContractSubdirectoryTypeEnforcementRule`)
- **Domain subdirectory types enforced** — `Enum/` must contain enums, `Handler/Command/` must implement `CommandHandler`, `Handler/Query/` must implement `QueryHandler`, `EventHandler/` must implement `DomainEventHandler`, `Middleware/` must implement `Middleware`, `Exception/` must implement `DomainException`. `Registry/Schema/` is exempt (`DomainSubdirectoryTypeEnforcementRule`)
- **All handlers in Domain** — `CommandHandler`, `QueryHandler`, and `DomainEventHandler` implementations must live in `App\Domain\`; if a handler needs infrastructure, use a `Contract` interface (`HandlersInDomainRule`)
- **Every command handler must collect domain events** — `CommandHandler` implementations must inject `EventCollector` and fire at least one event. Handlers that legitimately produce no events (infrastructure provisioning, data initialization) must declare `#[SkipDomainEvent(reason: '...')]` from `App\Contract\Attribute\SkipDomainEvent` (`CommandHandlerMustCollectEventsRule`)
- **Infrastructure must not collect domain events** — `EventCollector::collect()` is forbidden under `App\Infrastructure\` except inside `InMemoryEventCollector` (`NoInfrastructureEventCollectorCollectRule`)
- **Infrastructure must not inject the application buses** — `CommandBus` and `QueryBus` are forbidden on `App\Infrastructure\` classes except `App\Infrastructure\Bus\*` and `App\Infrastructure\Provider\*` (`NoApplicationBusInInfrastructureRule`)
- **Infrastructure cross-domain imports** — For Infrastructure classes outside the excluded wiring namespaces (bus, provider, persistence, logging, and related glue), any `use` of `App\Domain\{Other}\…` must resolve under `App\Domain\{Other}\Contract\…` whenever `{Other}` is not the adapter’s inferred home module (`InfrastructureCrossDomainImportsRule`, `InfrastructureCrossDomainGroupUseImportsRule`).
- **Update events must implement EntityUpdated** — every event class in `Contract/Event/` with a name ending in "Updated" must implement `App\Application\Event\EntityUpdated`. Update events carry a `list<PropertyChange>` with only the changed fields (old/new values). Handlers must not fire update events when nothing changes — compare old vs new state and skip the event if `$changes === []`. (`UpdatedEventMustImplementEntityUpdatedRule`)
- **No `assert()` calls** in `App\` namespace — use proper exceptions (`NoAssertInAppRule`)
- **No `mixed` native type** in `App\Domain` — parameters, return types, and properties must use specific types (`NoMixedInDomainRule`)
- **100% test coverage** of `app/Domain/` enforced by `phpunit.domain-coverage.xml`

## CQRS patterns

### Adding a command

Document contract types for consumers (class + property intent, no redundant tags) — see [Contract README](../Contract/README.md#documentation).

1. Create `app/Domain/{Context}/Contract/Command/{Name}Command.php` (in Contract — it's the public API):
   ```php
   /**
    * Describes what the command does in one or two sentences.
    */
   final readonly class {Name}Command implements \App\Contract\Command\Command
   {
       public function __construct(
           /** Meaning of this field for handlers and API callers. */
           public string $field,
       ) {}
   }
   ```

2. Create `app/Domain/{Context}/Handler/Command/{Name}Handler.php` (handler stays in Domain internals):
   ```php
   /** @implements CommandHandler<{Name}Command> */
   final readonly class {Name}Handler implements \App\Contract\Command\CommandHandler
   {
       public function __construct(
           private SomeDependency $dep,
           private \App\Contract\Event\EventCollector $eventCollector,
       ) {}

       public function handle(Command $command): void
       {
           // PHPStan resolves $command as {Name}Command via @implements
           // domain logic
           $this->eventCollector->collect(new {Name}Event(...));
       }
   }
   ```

   Every command handler must inject `EventCollector` and fire at least one domain event. For handlers that legitimately produce no events (infrastructure provisioning, data initialization), add `#[SkipDomainEvent(reason: '...')]` to the handler class. Enforced by `CommandHandlerMustCollectEventsRule`.

3. Register in `app/Infrastructure/Provider/BusServiceProvider.php` (see [Infrastructure README](../Infrastructure/README.md)):
   ```php
   {Name}Command::class => {Name}Handler::class,
   ```

4. Dispatch via `App\Contract\Bus\CommandBus::dispatch()`.

### Domain events vs direct service calls

Domain events are for **non-critical asynchronous side effects** (logging, cache invalidation, analytics). If a side effect is **critical to the command's success** (e.g., sending a password reset email), it must be executed synchronously via a domain service contract injected into the handler — not delegated to a domain event handler.

Example: `RequestPasswordResetHandler` calls `TemplatedEmailDispatcher::dispatch()` synchronously (critical — user expects the email). The `TemplatedEmailSent` event is then collected for async logging by `LogEmailOnSent` (non-critical follow-up).

All commands are automatically wrapped in a database transaction by the `WrapInTransaction` middleware. For commands that must not run in a transaction (DDL, migrations, landlord-connection writes), add `#[SkipTransaction(reason: '...')]` from `App\Contract\Attribute\SkipTransaction`.

### Adding a query

1. Create `app/Domain/{Context}/Contract/Query/{Name}Query.php` (in Contract — it's the public API):
   ```php
   /**
    * Describes what the query returns and when to use it.
    *
    * @implements Query<ReturnType>
    */
   final readonly class {Name}Query implements \App\Contract\Query\Query
   {
       public function __construct(
           /** Meaning of this field for handlers and API callers. */
           public string $field,
       ) {}
   }
   ```

2. Create `app/Domain/{Context}/Handler/Query/{Name}Handler.php`:
   ```php
   /** @implements QueryHandler<{Name}Query, ReturnType> */
   final readonly class {Name}Handler implements \App\Contract\Query\QueryHandler
   {
       public function __construct(private SomeDependency $dep) {}

       public function handle(Query $query): ReturnType
       {
           // PHPStan resolves $query as {Name}Query via @implements
       }
   }
   ```

3. Register in `BusServiceProvider` under `QueryBus`. Dispatch via `App\Contract\Bus\QueryBus::dispatch()`.
   PHPStan infers the return type from `Query<ReturnType>` — no `assert()` or `@var` needed at call sites.

## Access Context & Scope-Aware Queries

Queries returning lists of domain-scoped resources can implement `ScopeAwareQuery` to receive automatic scope filtering via the `ResolveScopeFilter` bus middleware. The middleware resolves the actor's `AccessScope` (All/Team/Own) and pre-resolves visible IDs, then creates a scoped copy of the query via `withAccessContext()`.

The `AccessContext` value object carries:
- `scope` — the resolved `AccessScope` enum
- `visibleIds` — `null` for unrestricted (All), or a concrete `list<string>` of visible IDs

Handlers read the access context and pass `visibleIds` to the repository:

```php
/** @implements ScopeAwareQuery<list<User>> */
#[RequiresPermission('users.list.read')]
final readonly class ListUsersQuery implements ScopeAwareQuery
{
    public function __construct(private ?AccessContext $accessContext = null) {}

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($accessContext);
    }

    public function accessContext(): ?AccessContext { return $this->accessContext; }
}
```

```php
final readonly class ListUsersHandler implements QueryHandler
{
    public function handle(Query $query): array
    {
        return $this->userRepository->all($query->accessContext()?->visibleIds);
    }
}
```

Not all queries need scope filtering — only queries returning lists of scoped resources implement `ScopeAwareQuery`. Single-entity queries (`GetUserByIdQuery`), counts, and internal lookups use plain `Query`.

## Paginated Queries

List queries that support pagination implement `PaginableQuery` alongside `Query<PaginatedResult<T>>`. When `pagination()` is `null` (no pagination requested), the handler returns all results wrapped in `PaginatedResult`. When a `Pagination` is provided, the handler delegates to the repository's `*Paginated()` method.

Repository interfaces expose separate paginated methods (`allPaginated()`, `findAllPaginated()`) alongside the original unpaginated methods, keeping backward compatibility for non-paginated consumers (tree views, dropdowns, console commands).

## Sorted Queries

List queries that support sorting implement `SortableQuery` alongside `Query`. When `sorting()` is `null` (no sorting requested), the handler applies a domain-appropriate default (e.g., users by name, roles by permission score). The `Sorting` value object carries a column name (a domain concept like `'name'` or `Sorting::PERMISSION_SCORE`) and a `SortDirection`. Infrastructure repositories translate these to SQL ORDER BY clauses, computing values for virtual columns like `permission_score` via SQL subqueries.

## Filtered Queries

List queries that support filtering implement `FilterableQuery` alongside `Query`. The `Filter` value object carries a column name, `FilterOperator` (Equals, Contains, In, Search), and a value. The `Search` operator fans out across multiple columns defined by the repository's `searchColumns()` method — the column field is ignored for search filters.

Handlers pass `$query->filters()` to the repository. Empty filter list = no filtering. Controllers own the column allowlist via `FILTERABLE_COLUMNS` constant and build `Filter` objects from request parameters (`?search=` for fulltext, `?filter[col]=` for column filters).

Infrastructure repositories apply filters via the `FiltersQuery` trait, which maps operators to SQL: `Equals` → `WHERE col = ?`, `Contains` → `unaccent(col) ILIKE unaccent(?)`, `In` → `WHERE col IN (?)`, `Search` → OR-group across `searchColumns()`. LIKE wildcards in user input are escaped via `escapeLike()`.

## Domain\*\Contract pattern

Each bounded context exposes a `Contract` sub-namespace containing types that other domains may import:

- **Entities**: `User`, `Team`, `Role`, `File`, `Label`, `Notification`, `Definition`, `Entry` — in `Domain/{Context}/Contract/Entity/`
- **Value objects**: `UserId`, `TeamId`, `RoleId`, `LabelId`, `FileId`, `NotificationId`, etc. — in `Domain/{Context}/Contract/ValueObject/`
- **Repository interfaces**: `UserRepository`, `TeamRepository`, `FileRepository`, etc. — in `Domain/{Context}/Contract/Repository/`
- **Service contracts**: `FileStorage` — in `Domain/File/Contract/Service/`, `TeamMembershipChecker` — in `Domain/Team/Contract/Service/`, `InviteLinkGenerator`, `PasswordResetBroker`, `PasswordManager`, `TwoFactorManager` — in `Domain/User/Contract/Service/`; password rotation policy reads/writes use `PasswordRotationSettingsRepository` and `PasswordHistoryRepository`, and tenant/user 2FA policy/state use `TwoFactorSettingsRepository`, `UserTwoFactorStateRepository`, and `EmailTwoFactorChallengeRepository` in `Domain/User/Contract/Repository/` (administrative reset of a user’s second factor uses `AdminResetUserTwoFactorCommand` / `AdminResetUserTwoFactorHandler`, permission `user_recovery.two_factor.update`).
- **Enums**: `NotificationChannel`, `VersionStatus`, `AccessScope` — in `Domain/{Context}/Contract/Enum/`
- **Domain events**: `UserCreated`, `UserInviteSent`, `UserInviteAccepted`, `PasswordChanged`, `PasswordResetRequested`, `PasswordResetCompleted`, `RoleDeleted`, `FileStored`, `FileDeleted`, etc. — in `Domain/{Context}/Contract/Event/`

Internal types (handlers, exceptions, domain-internal value objects, enums, services) stay in `Domain/{Context}/` organized by type (`ValueObject/`, `Enum/`, `Service/`, `Policy/`, `Constant/`) and are not importable cross-domain.

## Cross-domain communication

Bounded contexts must not depend on each other directly. Enforced by `DomainCrossDomainImportsRule` and `DomainCrossDomainGroupUseImportsRule`. Any domain may import `Domain\{Other}\Contract\*` — this is the intended cross-domain boundary. Direct imports of `Domain\{Other}\*` (non-Contract) are blocked.

### Adding a domain event

1. Create `app/Domain/{Context}/Contract/Event/{Name}.php`:
   ```php
   final readonly class {Name} implements \App\Contract\Event\DomainEvent
   {
       public function __construct(
           public string $someId,
           public string $relevantData,
           public \DateTimeImmutable $occurredAt,
       ) {}

       public function occurredAt(): \DateTimeImmutable
       {
           return $this->occurredAt;
       }
   }
   ```

   Events carry primitives (`string`, not value objects) for serialization safety. `DateTimeImmutable` is natively PHP-serializable.

2. Collect events in the handler via `EventCollector`:
   ```php
   $this->eventCollector->collect(new {Name}($id, $data, new \DateTimeImmutable()));
   ```

3. The `DispatchCollectedEvents` middleware flushes collected events after handler success.

### Adding an update domain event

Update events carry a `list<PropertyChange>` instead of individual fields. Each `PropertyChange` has `property`, `old`, `new`, and `sensitive` fields.

1. Create `app/Domain/{Context}/Constant/{Context}Fields.php` with field name constants.
2. Create the event implementing both `DomainEvent` and `EntityUpdated`:
   ```php
   final readonly class {Name}Updated implements \App\Contract\Event\DomainEvent, \App\Application\Event\EntityUpdated
   {
       /** @param list<\App\Application\Event\PropertyChange> $changes */
       public function __construct(
           public string $entityId,
           public array $changes,
           public \DateTimeImmutable $occurredAt,
       ) {}

       /** @return list<\App\Application\Event\PropertyChange> */
       public function changes(): array { return $this->changes; }

       public function occurredAt(): \DateTimeImmutable { return $this->occurredAt; }
   }
   ```
3. In the handler, compare old vs new state and only fire the event if changes exist:
   ```php
   $changes = $this->buildChanges($existing, $updated);
   if ($changes === []) {
       return;
   }
   $this->repository->update($updated);
   $this->eventCollector->collect(new {Name}Updated($id, $changes, new \DateTimeImmutable()));
   ```
4. For sensitive fields, use `new PropertyChange($field, null, null, sensitive: true)` to redact values.

Enforced by `UpdatedEventMustImplementEntityUpdatedRule`.

### Adding an event handler

1. Create handler implementing `App\Contract\Event\DomainEventHandler`.
2. Register in `BusServiceProvider` under `EventBus` handlers (see [Infrastructure README](../Infrastructure/README.md)):
   ```php
   {EventClass}::class => [{HandlerClass}::class],
   ```

3. Add `#[RetryPolicy(tries: N, backoff: [1, 5, 30], timeout: 60)]` — every event handler must explicitly declare its retry config.
4. Handlers run asynchronously via `HandleDomainEventJob` on the queue.

### Cross-context data

Never import from another context's internal types directly. Use the `Contract` sub-namespace for cross-domain types, or query on-demand via `QueryBus` or `CommandBus`.
