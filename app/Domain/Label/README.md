# Label Module

Universal labeling system — namespaced, polymorphic labels that can be applied to any entity type.

## Design

Labels are namespaced per entity type (`users`, `teams`, etc.). A label name is unique within its namespace. Labels are auto-deleted when orphaned (no remaining assignments).

### Schema

- `labels` — id, namespace, name (unique per namespace)
- `label_assignments` — label_id FK, labelable_id (polymorphic UUID, no FK)

### Permissions

| Action | Permission | Who checks |
|--------|-----------|-----------|
| Search labels | `labels.management.read` | Middleware via `#[RequiresPermission]` |
| Create new label | `labels.management.create` | Middleware via `#[RequiresPermission]` |
| Assign/remove label | Entity's update perm (e.g. `users.list.update`) | Entity controller |

`AssignLabelCommand` and `RemoveLabelCommand` use `#[SkipPermissionCheck]` because authorization is enforced per-entity at the controller level.

### Orphan Cleanup

- **On manual removal**: `RemoveLabelHandler` checks `hasAssignments()` after removing an assignment. If none remain, deletes the label.
- **On entity deletion**: `CleanupLabelsOnEntityDeleted` event handler listens to `UserDeleted`/`TeamDeleted` (via `EntityDeleted` contract). Removes all assignments for the deleted entity and deletes orphaned labels.

## Commands

| Command | Permission | Purpose |
|---------|-----------|---------|
| `CreateLabelCommand` | `labels.management.create` | Create a new label in a namespace |
| `AssignLabelCommand` | SkipPermissionCheck | Assign an existing label to an entity |
| `RemoveLabelCommand` | SkipPermissionCheck | Remove a label assignment (+ orphan cleanup) |

## Queries

| Query | Permission | Returns |
|-------|-----------|---------|
| `SearchLabelsQuery` | `labels.management.read` | `list<Label>` filtered by namespace + term |
| `GetEntityLabelsQuery` | SkipPermissionCheck | `list<Label>` for a specific entity |

## Events

- `LabelCreated` — new label added to catalog
- `LabelAssigned` — label assigned to entity
- `LabelRemoved` — label unassigned from entity
- `LabelDeleted` — orphaned label removed from catalog

## Adding Labels to a New Entity Type

1. Entity's `*Deleted` event must implement `EntityDeleted` contract
2. Register `CleanupLabelsOnEntityDeleted` for the new event in `BusServiceProvider`
3. Add label loading to entity's show/edit/list controllers
4. Add `syncLabels()` to entity's update controller
5. Add labels chip selector to entity's edit Blade view
6. Add label display to entity's list Blade view
