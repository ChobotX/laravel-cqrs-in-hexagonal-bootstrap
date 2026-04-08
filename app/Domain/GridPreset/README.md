# GridPreset Module

User-owned saved filter/sort/search presets for data grids. Each preset stores the complete grid state (filters, sorting, search term) and can be set as the default view for a specific grid.

## Architecture

Lightweight domain module with `#[SkipDomainEvent]` on command handlers — presets are a UI convenience with no downstream business effects.

## Entity

`GridPreset` — immutable value carrier with: id, userId, gridName, name, filters (JSON), sorting (JSON), search, isDefault, position.

## CQRS

### Commands
- `SaveGridPresetCommand` — create or update a preset (upsert by ID)
- `DeleteGridPresetCommand` — delete a preset (with ownership check)
- `SetDefaultGridPresetCommand` — set one preset as default, clearing others

### Queries
- `ListGridPresetsQuery` — returns all presets for a user+grid pair
- `GetDefaultGridPresetQuery` — returns the default preset or null

All commands and queries use `#[SkipPermissionCheck]` — users manage only their own presets. Ownership is enforced in handlers.

## Storage

`grid_presets` table in tenant schema. JSONB columns for `filters` and `sorting`. UUID primary key. Foreign key to `users` with cascade delete.

## Presentation

Three single-action controllers:
- `SaveGridPresetController` — POST `/grid-presets`
- `DeleteGridPresetController` — DELETE `/grid-presets/{presetId}`
- `SetDefaultGridPresetController` — PUT `/grid-presets/{presetId}/default`

Vue component: `DataGridPresets.vue` renders horizontal tab bar with preset buttons and a "Save as new view" dropdown (Teleport-positioned).
