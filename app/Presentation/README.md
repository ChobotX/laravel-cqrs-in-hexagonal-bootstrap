# Presentation Layer

HTTP controllers, console commands, views, and frontend. All classes must be `final` (traits excluded).

## Invokable controllers

Controllers in `App\Presentation\Http\Controller` must have a single `__invoke()` method. One route = one controller.

## Controller organization

- **Public API:** `App\Presentation\Http\Controller\Api\V1\{Context}\` — versioned REST endpoints at `/api/v1/`
- **Web:** `App\Presentation\Http\Controller\Web\{Context}\` — Blade page controllers

When adding a new API version, create `Controller\Api\V2\{Context}\` and add a `Route::prefix('v2')` group in `routes/api.php`. V1 controllers stay frozen.

## Form requests

Form requests extend `App\Presentation\Http\Request\FormRequest` (not Laravel's base).

**Use `routeString()`** to extract route parameters with type safety:
```php
$id = $this->routeString('id');
```

Enforced by `UseStrictRouteParametersRule` — direct `$this->route()` calls are forbidden in form requests.

## Controller request parameters

Controllers must not type-hint `Illuminate\Http\Request` directly. If a controller needs request data, it must use a custom `FormRequest` subclass. Controllers that take no request parameter are fine.

Enforced by `ControllerMustUseFormRequestRule` PHPStan rule.

## Controller dependencies

Controllers may only inject: `CommandBus`, `QueryBus`, `AuthenticatedUser`, `AuthorizationChecker`, `IdGenerator`, `Guard`. No domain services, repositories, or infrastructure. All data access goes through the bus. Nullable, union, and intersection types are unwrapped and checked individually.

Enforced by `ControllerDependenciesRule` PHPStan rule.

## Console commands

All Artisan commands must `use StrictArguments` (`App\Presentation\Console\Trait\StrictArguments`).

**Banned:** `$this->argument()`, `$this->option()` — these return `mixed`.

**Use instead:** `$this->stringArgument()`, `$this->intArgument()`, `$this->stringOption()`, `$this->nullableStringOption()`, `$this->intOption()`, `$this->boolOption()`.

Enforced by `UseStrictArgumentsInConsoleRule` PHPStan rule.

## Scope filtering is forbidden in controllers

Controllers must **never** resolve access scope or filter data by scope. Scope-based filtering is domain logic handled by the `ResolveScopeFilter` bus middleware. Controllers dispatch `ScopeAwareQuery` objects and receive already-filtered results.

Controllers may use `AuthorizationChecker::can()` for binary UI visibility checks (e.g., "should I render the Roles tab?"), but must never call `canWithScope()` or import scope-related types.

**Enforced by:**
- `NoScopeResolutionInPresentationRule` — blocks `canWithScope()` calls
- `testPresentationDoesNotDependOnTeamMembershipChecker` — blocks service import
- `testPresentationDoesNotDependOnAccessContext` — blocks `AccessContext`/`AccessScope` imports

## Controller command dispatch

Controllers must not dispatch bus messages inside loops. Looping over `->dispatch()` — whether commands or queries — is an N+1 pattern. Commands should use aggregate handlers (e.g., `SyncEntityLabelsCommand`). Queries should use batch queries that accept multiple IDs (e.g., `GetRolesForUsersQuery`, `GetLabelsForEntitiesQuery`).

Enforced by PHPStan rule `NoBusDispatchInControllerLoopsRule`.

## View rules

- **Dumb templates** — Blade views must contain zero business logic. All computation, formatting, and decision-making happens in controllers or dedicated view models before data reaches the template.
- **No non-Presentation references** — Blade templates must not reference any `App\*` namespace except `App\Presentation\*`. All data from other layers (authenticated user, tenant slug, access scopes) must be passed from controllers or shared via middleware using `View::share`. Enforced by `bin/lint-blade-layers.sh`.
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
| `label` | `string` | required | Tooltip text (`data-tooltip`) and `aria-label` for accessibility |
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
    icon="heroicon-o-finger-print" :label="__('messages.impersonation.start') . ' ' . $user->name" />
```

## `<x-tooltip>` component

Tooltip wrapper for any element. Uses the tooltip bridge (`resources/js/tooltip/tooltip.ts`) for positioning and display.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `text` | `?string` | `null` | Plain-text tooltip content |
| `position` | `string` | `'top'` | Preferred position: `top`, `bottom`, `left`, `right` |

**Slots:**

| Slot | Description |
|------|-------------|
| default | The trigger element |
| `content` | HTML tooltip content (takes priority over `text` prop) |

**Examples:**
```blade
{{-- Plain text --}}
<x-tooltip text="Edit user">
    <button aria-label="Edit user">...</button>
</x-tooltip>

{{-- HTML content --}}
<x-tooltip>
    <span>+3</span>
    <x-slot:content>
        <span class="flex flex-wrap gap-1">
            <span class="badge">Label A</span>
            <span class="badge">Label B</span>
        </span>
    </x-slot:content>
</x-tooltip>
```

Tooltips appear on hover and focus. The Blade bridge (`resources/js/tooltip/tooltip.ts`) also supports Escape key and scroll to dismiss. A Vue component (`resources/js/tooltip/Tooltip.vue`) is available for use inside Vue component trees with the same positioning logic — it dismisses on mouseleave and focusout.

## `<x-breadcrumb>` component

Hierarchical navigation breadcrumb. Renders a semantic `<nav>` with `<ol>` list. All items except the last are clickable links; the last item represents the current page (plain text with `aria-current="page"`). Renders nothing when there is only one item or no items.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `items` | `array` | `[]` | Array of `['label' => string, 'href' => string]` — `href` is optional on the last item |

**Examples:**
```blade
{{-- Index page --}}
<x-breadcrumb :items="[
    ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
    ['label' => __('messages.nav.users')],
]" />

{{-- Nested page --}}
<x-breadcrumb :items="[
    ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
    ['label' => __('messages.nav.registry'), 'href' => route('registry.definitions.index')],
    ['label' => $definition->name, 'href' => route('registry.definitions.show', [$definition->namespace, $definition->slug])],
    ['label' => __('messages.registry.entries.title')],
]" />
```

## `<x-badge-list>` component

Renders a list of label badges with overflow. Shows up to `max` labels inline, with a "+N" tooltip for the rest.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `labels` | `array` | required | Array of label objects with `->name->value` |
| `max` | `int` | `2` | Maximum visible badges before collapsing |

**Example:**
```blade
<x-badge-list :labels="$userLabels[$user->id->value] ?? []" />
<x-badge-list :labels="$teamLabels[$team->id->value]" :max="3" />
```

## Tenant middleware

Two middleware handle tenant resolution, applied globally to `web` and `api` stacks via `bootstrap/app.php`:

- **`ResolveTenantMiddleware`** (prepended) — extracts subdomain from `Host` header, calls `TenantBootstrapper::bootstrapByDomain()` to resolve tenant and switch schema. Root domain requests (no subdomain) pass through without resolving.
- **`EnsureTenantResolved`** (prepended, after resolve) — returns 404 if no tenant was resolved. Fail-safe: all routes require a tenant unless explicitly excluded.

Root domain routes (`routes/root.php`) opt out via `Route::withoutMiddleware(EnsureTenantResolved::class)`.

Both middleware depend only on Contract interfaces (`TenantBootstrapper`, `TenantContext`) — no Infrastructure imports.

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

## `<x-table-empty-state>` component

Empty state row for data tables. Renders a centered icon and message spanning all columns. Used inside `@forelse`/`@empty` blocks.

**Props:**

| Prop | Type | Description |
|------|------|-------------|
| `colspan` | `int` | Number of table columns to span |
| `message` | `string` | Empty state message text |

**Usage:**
```blade
<tbody>
    @forelse ($result->items as $item)
        <tr>...</tr>
    @empty
        <x-table-empty-state colspan="3" :message="__('messages.users.empty')" />
    @endforelse
</tbody>
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

## `<x-pagination>` component

Renders pagination controls for `PaginatedResult` objects. Hidden when `totalPages() <= 1`.

**Props:**

| Prop | Type | Description |
|------|------|-------------|
| `result` | `PaginatedResult` | The paginated result to render controls for |

Generates URLs preserving existing query params. Shows "Showing X to Y of Z results", prev/next arrows, and a sliding window of page numbers with ellipsis.

**Usage:**
```blade
<x-pagination :result="$result" />
```

**Shared `PaginationRequest`** (`App\Presentation\Http\Request\Web\PaginationRequest` / `App\Presentation\Http\Request\PaginationRequest`) validates `page`, `per_page`, `sort`, and `direction` query params and provides `pagination(): Pagination` and `sorting(): ?Sorting` helpers. Used by all list controllers (web and API). The `sorting()` method returns `null` when no `sort` param is present.

## `<x-sortable-header>` component

Clickable table column header with sort direction indicator. Used in grid list views to enable column sorting.

**Props:**

| Prop | Type | Description |
|------|------|-------------|
| `column` | `string` | Column identifier (matches the database column or sort key) |
| `label` | `string` | Visible header text and link title |
| `sorting` | `Sorting` | The currently active sorting (column + direction) |

Generates URLs preserving existing query params, resetting `page` to 1. Active column shows directional chevron (indigo); inactive columns show a neutral up-down chevron (gray).

**Usage:**
```blade
<tr>
    <x-sortable-header column="name" :label="__('messages.users.user')" :sorting="$sorting" />
    <x-sortable-header column="email" :label="__('messages.users.email')" :sorting="$sorting" />
    <th scope="col">{{ __('messages.users.actions') }}</th>
</tr>
```

Controllers must pass the effective `Sorting` VO to the view as `$sorting`. Each controller defines its own `SORTABLE_COLUMNS` constant and validates the request sort column against it:

```php
private const array SORTABLE_COLUMNS = ['name', 'email'];

$defaultSorting = new Sorting('name', SortDirection::Asc);
$requestSorting = $paginationRequest->sorting();
$sorting = $requestSorting !== null && in_array($requestSorting->column, self::SORTABLE_COLUMNS, true)
    ? $requestSorting
    : $defaultSorting;
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

## URL generation — never use `request()->url()`

**Banned:** `request()->url()`, `request()->fullUrl()`, `request()->getUri()` — in both PHP and Blade.

These return the raw HTTP scheme from the underlying request. When a reverse proxy terminates SSL and forwards HTTP to PHP, they return `http://` URLs. With `SESSION_SECURE_COOKIE=true`, the browser only sends session cookies over HTTPS — so any link using a raw `http://` URL silently drops the session, causing a 302 redirect to login.

**Use instead:**

| Banned | Safe alternative | Notes |
|---|---|---|
| `request()->url()` | `url()->current()` | Current URL without query string, correct scheme |
| `request()->fullUrl()` | `url()->full()` | Current URL with query string, correct scheme |
| `request()->getUri()` | `url()->full()` | Same as above |
| — | `route('name')` | Named route, always correct |

**Enforced by:**
- `NoRawRequestUrlRule` PHPStan rule — blocks `$request->url()` / `fullUrl()` / `getUri()` in `App\Presentation`
- `lint:blade:url` (`bin/lint-blade-url.sh`) — blocks the same patterns in Blade templates

## Session expiry handling

When a user's session expires, the app redirects them to login and returns them to where they were after re-authenticating. The intended URL is passed via `?redirect=` query parameter (not session) because the cookie session driver cannot persist session changes when an exception interrupts the middleware pipeline. Three mechanisms work together:

1. **GET requests** — `Authenticate` middleware redirects to `/login?redirect=<requestUri>` (configured via callable in `bootstrap/app.php`). `ShowLoginController` reads `?redirect=`, validates it with `SafeRedirectValidator`, and stores it as `url.intended` in session. After login, `redirect()->intended()` in `LoginController` returns the user to the original page.
2. **POST/PUT/DELETE requests** — CSRF validation fails before auth middleware runs. The `HttpException(419)` handler in `bootstrap/app.php` redirects to `/login?redirect=<referer>` (validated by `SafeRedirectValidator`). Authenticated users with a CSRF mismatch (e.g. two-tab scenario) get a redirect back instead.
3. **AJAX requests** — `session-guard.ts` wraps `window.fetch` to detect 401/419 responses from same-origin requests and redirects the browser to `/login?redirect=<currentPath>`.

`SafeRedirectValidator` (`App\Presentation\Http\Security`) prevents open redirect attacks by only allowing relative paths (not protocol-relative `//`) or same-host absolute URLs.

## Auth flows

Guest routes (`routes/web.php`, `guest` middleware group):

- **Login** — `GET /login` (ShowLoginController), `POST /login` (LoginController)
- **Invite acceptance** — `GET /invite/{userId}` (ShowAcceptInviteController), `POST /invite/{userId}` (AcceptInviteController). Uses `signed` middleware — invite links are HMAC-signed with 72h expiry via `URL::temporarySignedRoute()`. User sets their password to activate their account.
- **Password reset** — `GET /forgot-password` (ShowForgotPasswordController), `POST /forgot-password` (ForgotPasswordController), `GET /reset-password/{token}` (ShowResetPasswordController), `POST /reset-password` (ResetPasswordController). Uses Laravel's Password Broker for token generation/validation. Rate-limited at 3 requests/min per email+IP.

Authenticated routes:

- **Resend invite** — `POST /users/{userId}/resend-invite` (ResendInviteController). Available for non-activated users. Requires `users.list.update` permission.

All auth controllers use `#[SkipPermissionCheck]` (guest actions) or `#[RequiresPermission]` (admin actions).

## Shared Vue components

Reusable Vue components live in `resources/js/shared/`. These mirror the styling of their Blade counterparts to ensure visual consistency across server-rendered grids and Vue-powered lists.

### `ActionButton`

Vue equivalent of `<x-action-button>`. Icon-only button for row actions in Vue lists.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | `string` | required | Tooltip text (`data-tooltip`) and `aria-label` |
| `variant` | `string` | `'default'` | `'default'` (indigo hover) or `'danger'` (red hover) |

Uses a default slot for the icon SVG (`h-5 w-5`). Styling matches the Blade component: `rounded-lg p-2 text-gray-400`.

**Example:**
```vue
<ActionButton variant="danger" :label="trans('messages.notifications.delete')" @click="handleDelete">
    <svg class="h-5 w-5" ...><!-- heroicon-o-trash --></svg>
</ActionButton>
```

## Vue frontend rules (when applicable)

- **Minimal frontend logic** — JavaScript should only handle UI interactions (toggles, transitions, form UX). All data shaping, filtering, sorting, and formatting must happen server-side. The backend serves ready-to-render payloads.
- **Internal API routes** — Vue frontend consumes internal API routes defined in `routes/internal_api.php` (registered via `bootstrap/app.php` `then:` callback with the `web` middleware stack), not the public API. `routes/api.php` is reserved for the public/external API and must stay decoupled from frontend concerns. `routes/web.php` is for Blade page routes only.
- **Dumb components** — Vue components receive well-prepared props and render them. Business logic stays in the backend, not in computed properties or watchers.
- **Reusable components** — follow SRP and DRY. Extract shared UI patterns into small, focused components rather than duplicating templates.
- **Composition over duplication** — when a component needs a variant with different data flow (e.g. static vs lazy-loaded), create a wrapper component that composes the base component rather than forking or adding mode flags. The wrapper owns the new concern (fetch, debounce, state sync) and feeds the base component via props and events. Example: `LazyChipSelector` wraps `ChipSelector` — it handles server-side search and feeds results as `options`, while `ChipSelector` stays a pure display/interaction component.

## Notification components

The notification system uses internal API routes (`routes/internal_api.php`) consumed by Vue components mounted to Blade templates via `data-*` attributes.

**Controllers** (`Controller\Web\Notification\`): `ListNotificationsController`, `CountUnreadNotificationsController`, `MarkNotificationAsReadController`, `MarkAllNotificationsAsReadController`, `DeleteNotificationController`, `ShowNotificationsController`. All use `#[SkipPermissionCheck]` — ownership enforced in domain handlers.

**Vue components** (`resources/js/notification/`):
- `NotificationBell.vue` — topbar bell icon + dropdown, mounted to `#app-notification-bell`
- `NotificationList.vue` — full page list with filters/pagination, mounted to `#app-notification-list`
- `NotificationItem.vue` — single notification display (compact mode for bell, full for list)
- `NotificationPreferences.vue` — preference grid for profile, outputs hidden inputs for form submission

**Real-time** (`notification-echo.ts`): Laravel Echo + Reverb WebSocket subscription to `private-notifications.{userId}`. Initialized in `notification-bell-app.ts`. Listens for `NotificationReceived` and `UnreadCountUpdated` events.

**State management** (`notification-store.ts`): Reactive store (same pattern as `toast-queue.ts`) shared between bell and echo modules.

## Form action enums

Multi-action controllers that dispatch different commands based on a form `_action` field must use a backed string enum for the action discriminator. Enums live alongside their form request:

- `TeamMemberAction` (`App\Presentation\Http\Request\Web\Team`) — `add_member`, `remove_member`
- `UserPermissionAction` (`App\Presentation\Http\Request\Web\Authorization`) — `assign_role`, `revoke_role`, `set_override`, `remove_override`
- `NotificationFilter` (`App\Presentation\Http\Request\Web\Notification`) — `unread`, `read`

Form requests return the typed enum from `action()`. Controllers compare against enum cases directly.

The Presentation layer may depend on Application, Domain, and Contract. It must not depend on Infrastructure.
