# Presentation Layer

HTTP controllers, console commands, views, and frontend. All classes must be `final` (traits excluded).

## Invokable controllers

Controllers in `App\Presentation\Http\Controller` must have a single `__invoke()` method. One route = one controller.

## Form requests

Form requests extend `App\Presentation\Http\Request\FormRequest` (not Laravel's base).

**Use `routeString()`** to extract route parameters with type safety:
```php
$id = $this->routeString('id');
```

Enforced by `UseStrictRouteParametersRule` — direct `$this->route()` calls are forbidden in form requests.

## Console commands

All Artisan commands must `use StrictArguments` (`App\Presentation\Console\Trait\StrictArguments`).

**Banned:** `$this->argument()`, `$this->option()` — these return `mixed`.

**Use instead:** `$this->stringArgument()`, `$this->intArgument()`, `$this->stringOption()`, `$this->nullableStringOption()`, `$this->intOption()`, `$this->boolOption()`.

Enforced by `UseStrictArgumentsInConsoleRule` PHPStan rule.

## View rules

- **Dumb templates** — Blade views must contain zero business logic. All computation, formatting, and decision-making happens in controllers or dedicated view models before data reaches the template.
- **Backend over frontend** — prefer server-side calculations over client-side. Templates receive ready-to-render data.
- **Reusable components** — split views into small, single-responsibility Blade partials/components (`resources/views/components/`). Follow SRP and DRY — extract shared UI into components rather than duplicating markup across pages.
- **Blade formatting** — all `.blade.php` files must pass `blade-formatter --check-formatted`. Config in `.bladeformatterrc.json` enforces: 4-space indent, 120-char line width, force-aligned attribute wrapping (min 2 attrs), code-guide HTML attribute ordering, Tailwind class sorting, no multiple empty lines. Run `composer format:blade` to auto-fix.

## `<x-action-button>` component

Permission-gated action button for table rows. Every action button **must** specify either `permission` or `skip-permission` — omitting both causes the button to render nothing (fail-safe).

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `permission` | `?string` | `null` | Permission key checked via `@hasPermission` |
| `skip-permission` | `bool` | `false` | Explicit opt-out from permission gating |
| `href` | `?string` | `null` | Renders as `<a>` link |
| `action` | `?string` | `null` | Renders as `<form>` + `<button>` |
| `method` | `string` | `'POST'` | HTTP method for form variant |
| `icon` | `string` | required | Heroicon component name (e.g. `heroicon-o-pencil-square`) |
| `label` | `string` | required | Aria-label for accessibility |
| `variant` | `string` | `'default'` | `'default'` (indigo hover) or `'danger'` (red hover) |
| `confirm` | `bool` | `false` | Shows confirmation dialog before submit |
| `confirm-title` | `?string` | `null` | Confirmation dialog title |
| `confirm-message` | `?string` | `null` | Confirmation dialog message |

**Examples:**
```blade
{{-- Permission-gated link --}}
<x-action-button permission="users.list.update" :href="route('users.edit', $user->id)"
    icon="heroicon-o-pencil-square" :label="__('messages.users.edit_action') . ' ' . $user->name" />

{{-- Permission-gated destructive action --}}
<x-action-button permission="users.list.delete" :action="route('users.destroy', $user->id)"
    method="DELETE" icon="heroicon-o-trash" :label="__('messages.users.delete_action') . ' ' . $user->name"
    variant="danger" confirm :confirm-title="__('messages.users.delete_confirm_title')"
    :confirm-message="__('messages.users.delete_confirm_message', ['name' => $user->name])" />

{{-- Explicitly ungated --}}
<x-action-button skip-permission :action="route('impersonation.start', $user->id)"
    icon="heroicon-o-eye" :label="__('messages.impersonation.start') . ' ' . $user->name" />
```

## `CheckPermission` middleware

Route middleware (`App\Presentation\Http\Middleware\CheckPermission`) that enforces permission gating on controllers via the `#[RequiresPermission]` attribute.

**How it works:**
1. Resolves the controller class from the current route
2. Reads `#[RequiresPermission('permission.key')]` attribute via reflection
3. If present: checks permission via `AuthorizationChecker->can()`, throws `PermissionDeniedException` on failure
4. If absent: passes through (not every controller needs auth — e.g. login, dashboard)

**Usage:** Add the attribute to the controller class:
```php
#[RequiresPermission('users.roles.update')]
final readonly class ShowEditRoleController
{
    // No manual permission check needed — middleware handles it
}
```

Registered in `bootstrap/app.php` as a web middleware. Eliminates boilerplate permission checks from controllers.

## `<x-primary-button>` component

Permission-gated CTA button for create/submit/cancel actions. Renders as `<a>` (with `href`), `<form>+<button>` (with `action`), or `<button type="submit">` (neither). Fail-safe: renders nothing without `permission` or `skip-permission`.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `permission` | `?string` | `null` | Permission key checked via `@hasPermission` |
| `skip-permission` | `bool` | `false` | Explicit opt-out from permission gating |
| `href` | `?string` | `null` | Renders as `<a>` link |
| `action` | `?string` | `null` | Renders as `<form>` + `<button>` (POST + CSRF) |
| `method` | `string` | `'POST'` | HTTP method for form variant |
| `label` | `string` | required | Button text, title, and aria-label |
| `variant` | `string` | `'primary'` | `'primary'` (indigo), `'secondary'` (gray border), `'amber'`, `'login'` (full-width) |

**Examples:**
```blade
{{-- Permission-gated link --}}
<x-primary-button permission="users.list.create" :href="route('users.create')"
    :label="__('messages.users.create_action')" />

{{-- Submit button inside a permission-wrapped form --}}
<x-primary-button skip-permission :label="__('messages.permissions.add_role')" />

{{-- Cancel/back link --}}
<x-primary-button skip-permission variant="secondary" :href="route('roles.index')"
    :label="__('messages.roles.cancel')" />

{{-- Standalone form action --}}
<x-primary-button skip-permission variant="amber" :action="route('impersonation.stop')"
    :label="__('messages.impersonation.stop')" />
```

## `<x-icon-button>` component

Permission-gated icon-only submit button for use inside existing forms (e.g. remove role tag). Fail-safe: renders nothing without `permission` or `skip-permission`.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `permission` | `?string` | `null` | Permission key checked via `@hasPermission` |
| `skip-permission` | `bool` | `false` | Explicit opt-out from permission gating |
| `icon` | `string` | required | Heroicon component name |
| `label` | `string` | required | Title and aria-label |
| `class` | `string` | `'text-gray-400 ...'` | Custom CSS classes |

## `<x-control-button>` component

UI control button (sidebar toggle, dropdown trigger) that accepts slot content and merges custom attributes. Not permission-gated — these are layout controls, not domain actions.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | `string` | required | Title and aria-label |
| `class` | `string` | `''` | CSS classes |

Accepts arbitrary HTML attributes via Blade attribute forwarding (e.g. `data-sidebar-close`).

## `<x-nav-link>` component

Permission-gated sidebar navigation link. Fail-safe: renders nothing without `permission` or `skip-permission`.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `permission` | `?string` | `null` | Permission key checked via `@hasPermission` |
| `skip-permission` | `bool` | `false` | Explicit opt-out from permission gating |
| `href` | `string` | required | Link URL |
| `icon` | `string` | required | Heroicon component name |
| `label` | `string` | required | Visible text, title |
| `active` | `bool` | `false` | Current route match (adds `aria-current=page`) |

**Example:**
```blade
<x-nav-link permission="users.list.read" :href="route('users.index')"
    icon="heroicon-o-users" :label="__('messages.nav.users')"
    :active="request()->routeIs('users.*')" />
```

## `<x-topbar-button>` component

Permission-gated topbar form action button (icon-only). Fail-safe: renders nothing without `permission` or `skip-permission`.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `permission` | `?string` | `null` | Permission key checked via `@hasPermission` |
| `skip-permission` | `bool` | `false` | Explicit opt-out from permission gating |
| `action` | `string` | required | Form action URL |
| `icon` | `string` | required | Heroicon component name |
| `label` | `string` | required | Title and aria-label |
| `variant` | `string` | `'default'` | `'default'` (gray) or `'amber'` (for impersonation) |

**Example:**
```blade
<x-topbar-button skip-permission :action="route('impersonation.stop')"
    icon="heroicon-o-arrow-uturn-left" :label="__('messages.impersonation.stop')"
    variant="amber" />
```

## Vue frontend rules (when applicable)

- **Minimal frontend logic** — JavaScript should only handle UI interactions (toggles, transitions, form UX). All data shaping, filtering, sorting, and formatting must happen server-side. The backend serves ready-to-render payloads.
- **Internal API routes** — Vue frontend consumes internal API routes defined in `routes/internal_api.php`, not the public API. `routes/api.php` is reserved for the public/external API and must stay decoupled from frontend concerns. `routes/web.php` is for Blade page routes only.
- **Dumb components** — Vue components receive well-prepared props and render them. Business logic stays in the backend, not in computed properties or watchers.
- **Reusable components** — follow SRP and DRY. Extract shared UI patterns into small, focused components rather than duplicating templates.

The Presentation layer may depend on Application, Domain, and Contract. It must not depend on Infrastructure.
