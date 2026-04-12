# Registry Module

Dynamic schema-driven CRUD system for enumerations and non-core entities. Tenants define data types (definitions) at runtime with versioned JSON Schemas, then manage instances (entries) through generated forms.

## Design

Definitions describe what an entry looks like — a namespace + slug identity with versioned schemas. Each schema is a list of typed fields (string, integer, reference, file, repeater, etc.). Entries are instances validated against a pinned schema version.

### Schema

- `definitions` — id, namespace, slug, name (unique per namespace+slug)
- `definition_versions` — id, definition_id FK, version, body (JSONB), status (draft/active/deprecated)
- `entries` — id, definition_id FK, definition_version, namespace, title, data (JSONB), created_by_user_id (FK users.id, cascade delete), lock_version

### Type-Safe Schema Objects

Schema definitions use typed PHP objects in `App\Domain\Registry\Schema\`:

| Field Type | Description | Form Rendering |
|---|---|---|
| `StringField` | Text, optional multiline (textarea) | `<input>` or `<textarea>` |
| `IntegerField` | Whole number with optional min/max | `<input type="number">` |
| `NumberField` | Decimal with optional min/max | `<input type="number" step="any">` |
| `BooleanField` | True/false | `<input type="checkbox">` |
| `DateField` | Date value | `<input type="date">` |
| `EmailField` | Email address | `<input type="email">` |
| `ReferenceField` | Select populated from another definition's entries | `<select>` via internal API |
| `FileField` | File upload linked to File domain | Upload widget |
| `RepeaterField` | Group of fields that repeat (1+N or N items) | Add/remove rows |
| `ObjectField` | Nested fieldset | Recursive render |

Schema objects serialize to JSON Schema 2020-12 for storage via `SchemaSerializer`.

### Permissions

| Action | Permission |
|---|---|
| Read definitions | `registry.definitions.read` |
| Create definition | `registry.definitions.create` |
| Update definition / manage versions | `registry.definitions.update` |
| Delete definition | `registry.definitions.delete` |
| Read entries | `registry.entries.read` |
| Create entry | `registry.entries.create` |
| Update entry | `registry.entries.update` |
| Delete entry | `registry.entries.delete` |

### Versioning

- Versions are sequential per definition (1, 2, 3...)
- New versions start as `Draft`, must be explicitly `Activated`
- Only one `Active` version per definition at a time
- Activating a version deprecates the previous active version
- Entries pin to their creation version — they don't auto-migrate

### Reference Fields

Selects always reference another definition's entries (no inline options). A `Car` definition's manufacturer field points to a `CarManufacturer` definition. The form fetches options via internal API, displaying each entry's `title`.

### Reference Validation

`ReferenceValidator` validates that reference field values in entry data point to existing entries. Shared by `CreateEntryHandler` and `UpdateEntryHandler`. Recursively walks `ReferenceField`, `RepeaterField`, and `ObjectField` nested structures.

## Commands

| Command | Permission | Purpose |
|---|---|---|
| `CreateDefinitionCommand` | `registry.definitions.create` | Create a new definition |
| `UpdateDefinitionCommand` | `registry.definitions.update` | Update definition name |
| `DeleteDefinitionCommand` | `registry.definitions.delete` | Delete definition (fails if entries exist) |
| `CreateDefinitionVersionCommand` | `registry.definitions.update` | Add a new schema version (Draft) |
| `ActivateDefinitionVersionCommand` | `registry.definitions.update` | Activate a version |
| `DeprecateDefinitionVersionCommand` | `registry.definitions.update` | Deprecate a version |
| `CreateEntryCommand` | `registry.entries.create` | Create entry (validates against active version) |
| `UpdateEntryCommand` | `registry.entries.update` | Update entry (validates against pinned version) |
| `DeleteEntryCommand` | `registry.entries.delete` | Delete entry |

## Queries

| Query | Permission | Returns |
|---|---|---|
| `GetDefinitionByIdQuery` | `registry.definitions.read` | `?Definition` |
| `GetDefinitionBySlugQuery` | `registry.definitions.read` | `?Definition` |
| `ListDefinitionsQuery` | `registry.definitions.read` | `PaginatedResult<Definition>` |
| `ListDefinitionVersionsQuery` | `registry.definitions.read` | `list<DefinitionVersion>` |
| `GetActiveDefinitionVersionQuery` | `registry.definitions.read` | `?DefinitionVersion` |
| `GetSerializedSchemaQuery` | `registry.definitions.read` | `?JsonSchema` (serialized for Vue) |
| `GetEntryByIdQuery` | `registry.entries.read` | `?Entry` |
| `ListEntriesQuery` | `registry.entries.read` | `PaginatedResult<Entry>` (filterable, sortable) |
| `ListEntriesByDefinitionSlugQuery` | `registry.entries.read` | `list<Entry>` |
| `ListDefinitionNamespacesQuery` | `registry.definitions.read` | `list<string>` |

## Events

- `DefinitionCreated`, `DefinitionUpdated`, `DefinitionDeleted`
- `DefinitionVersionCreated`, `DefinitionVersionActivated`, `DefinitionVersionDeprecated`
- `EntryCreated`, `EntryUpdated`, `EntryDeleted` (implements `App\Contract\Event\EntityDeleted` → triggers generic share/label cleanup)

## Ownership & Sharing

Every entry carries a required `created_by_user_id` (FK `users.id`, cascade delete). `CreateEntryController` injects the authenticated user's id into the `CreateEntryCommand`; the handler persists it on the entry.

Ownership unlocks two things:

- **Scope-filtered listing**: `ListEntriesQuery` implements `ScopeAwareQuery` + `ShareableScopeQuery` with resource type `'entry'`. Under `Own` scope the user sees entries they created plus entries explicitly shared with them; under `Team` scope they also see entries owned by users in their team hierarchy. Filtering happens at the DB level via `App\Infrastructure\Eloquent\ScopesOwnedQuery` (used by `EloquentEntryRepository`).
- **Per-record sharing**: Any user with `registry.entries.update` can share an entry with any user they can see via the generic share API. See [Authorization module — Record Sharing](../Authorization/README.md#record-sharing) for the end-to-end contract.

When an entry is deleted, its shares are revoked automatically via the generic `CleanupSharesOnEntityDeleted` event handler (no entry-specific code needed).
