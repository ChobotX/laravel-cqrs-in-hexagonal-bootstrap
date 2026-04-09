# Feature Flag Module

Per-tenant feature flags with code-defined defaults and database overrides.

## Architecture

Flags are defined in `config/feature-flags.php` organized by groups. Each tenant can override defaults via the database. Only users with `feature_flags.management.read/update` permission (super-admin by default) can manage flags.

Every flag has two dimensions: **enabled** (on/off toggle) and **value** (the configured data). For boolean flags, enabled IS the value. For select/input flags, the toggle controls whether the feature is active, and the value configures its behavior when enabled.

## Flag Types

| Type | Config `default` | Stored as | Validation |
|------|------------------|-----------|------------|
| **Boolean** | `true`/`false` (normalized to `'1'`/`'0'`) | `'0'` or `'1'` | Exact match |
| **Select** | String from `options` list | One of `options` | Must be in `options` |
| **Input** | Any string | Any string | Must match `pattern` regex (if defined) |

Select and input types also accept an `enabled` key in config (defaults to `true` if omitted).

## Resolution Chain

Config default → DB tenant override → Per-tenant cache (TTL from `FEATURE_FLAG_CACHE_TTL` env, default 300s).

Cache is invalidated **synchronously** in command handlers for immediate admin feedback.

## Usage

### Backend (Domain handlers)

Inject `FeatureFlagChecker` from `App\Domain\FeatureFlag\Contract\Service\`:

```php
if ($this->featureFlagChecker->isEnabled('registry.schema-builder')) {
    // Schema builder is active
}
```

### Blade templates

```blade
@feature('registry.schema-builder')
    {{-- Renders when flag is enabled --}}
@else
    {{-- Renders when disabled --}}
@endfeature
```

### Vue / TypeScript

```typescript
import { isFeatureEnabled, featureValue } from './feature-flags/feature-flags';

if (isFeatureEnabled('registry.schema-builder')) { ... }
```

Flag state is passed to the frontend via a `<meta name="feature-flags">` tag in the layout.

## Adding a new flag

1. Add the flag to `config/feature-flags.php` under the appropriate group
2. Use `FeatureFlagChecker`, `@feature`, or the TypeScript helpers in code
3. No migration needed — new flags use their config default until overridden

## Commands & Queries

| Type | Class | Permission |
|------|-------|-----------|
| Command | `UpdateFeatureFlagCommand` | `feature_flags.management.update` |
| Command | `ResetFeatureFlagCommand` | `feature_flags.management.update` |
| Query | `ListFeatureFlagsQuery` | `feature_flags.management.read` |
| Query | `GetFeatureFlagQuery` | `feature_flags.management.read` |
| Query | `GetAllFeatureFlagValuesQuery` | Skip (all authenticated users) |

## Domain Events

- `FeatureFlagUpdated` — key, value, enabled, occurredAt
- `FeatureFlagReset` — key, occurredAt

## Management UI

Web routes:

- `GET /feature-flags` — Vue DataGrid with search, sort, pagination, presets, group filter, inline toggles
- `GET /feature-flags/{key}` — edit page (toggle + value for select/input types)
- `PUT /feature-flags/{key}` — update flag enabled state and/or value
- `DELETE /feature-flags/{key}` — reset to default

Internal API (Vue DataGrid):

- `GET /internal-api/feature-flags/list` — JSON endpoint for DataGrid (in-memory filter/sort/paginate over config-defined flags)
- `PATCH /internal-api/feature-flags/{key}/toggle` — toggle enabled state via JSON
