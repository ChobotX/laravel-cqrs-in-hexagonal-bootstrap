# Authorization Module

Enterprise-grade authorization with hierarchical permissions, RBAC, per-user overrides, multi-tenant teams, and user impersonation.

## Architecture Overview

### Permission Hierarchy (4-tier)

```
Module (e.g. "users", "crm")
  └── Feature (e.g. "list", "contacts", "roles")
       └── Action (read | create | update | delete)
            └── Scope (all | team | own)
```

Three access scopes: `All` > `Team` > `Own`. The `Own` scope implicitly includes shared resources — there is no separate `Shared` scope.

Higher-level grants cascade down:
- `users` grants everything under `users.*.*`
- `users.list` grants all actions under `users.list.*`

### Resolution Algorithm (deny-wins)

1. If user has super-admin role (system role) → granted with scope `All`
2. If impersonating → use impersonated user's permissions (no super-admin bypass)
3. Collect all role permissions for user (union of all assigned roles)
4. Apply permission inheritance (module → features → actions)
5. Merge with per-user overrides: **any explicit deny wins** over any grant
6. For scope: when multiple grants exist, most permissive scope wins (`All` > `Team` > `Own`)

### Key Contracts

- `AuthorizationChecker` — main entry point for permission checks
- `AuthenticatedUser` — provides current user context including impersonation
- `ImpersonationManager` — manages impersonation sessions
- `TeamMembershipChecker` — resolves team memberships with descendant expansion for `AccessScope::Team` filtering (see [Team module](../Team/README.md))

## Adding a New Module

1. Register in `config/authorization.php`:

```php
'modules' => [
    'crm' => [
        'label' => 'CRM',
        'features' => [
            'contacts' => [
                'label' => 'Contacts',
                'actions' => ['read', 'create', 'update', 'delete'],
            ],
        ],
    ],
],
```

2. Add `#[RequiresPermission]` to your commands/queries:

```php
#[RequiresPermission('crm.contacts.create')]
final readonly class CreateContactCommand implements Command { ... }
```

3. Update default role seeders if needed.

## Adding a Feature to an Existing Module

Just add it to the config. The permission matrix UI will pick it up automatically.

## Guarding a Command/Query

Every `Command` and `Query` class MUST have either `#[RequiresPermission]` or `#[SkipPermissionCheck]`. This is enforced by a PHPStan rule at static analysis time.

```php
use App\Application\Authorization\RequiresPermission;

#[RequiresPermission('users.list.create')]
final readonly class CreateUserCommand implements Command { ... }
```

The `AuthorizeAction` bus middleware reads this attribute and checks permissions before the handler runs.

## Skipping Authorization

For commands/queries that intentionally skip authorization (e.g., login, public endpoints):

```php
use App\Application\Authorization\SkipPermissionCheck;

#[SkipPermissionCheck(reason: 'Public authentication endpoint')]
final readonly class GetUserByEmailQuery implements Query { ... }
```

The `reason` parameter is required — forces a conscious decision.

## Record-Level Access (Scope Filtering)

Scope filtering is handled transparently by the `ResolveScopeFilter` bus middleware. Controllers never resolve scope — they dispatch a `ScopeAwareQuery` and the middleware enriches it with an `AccessContext` before the handler runs.

**Flow:**

1. Controller dispatches `ListUsersQuery` (no scope info)
2. `AuthorizeAction` middleware checks binary access (can/deny)
3. `ResolveScopeFilter` middleware reads `#[RequiresPermission]`, calls `canWithScope()`, resolves visible IDs via `TeamMembershipChecker`, creates a new query with `AccessContext`
4. Handler reads `accessContext()?->visibleIds` and passes to repository
5. Repository applies `WHERE IN (...)` at the SQL level

**`AccessContext` semantics:**
- `visibleIds = null` — unrestricted (All scope, no SQL filter)
- `visibleIds = ['id1', 'id2']` — restrict to these IDs (Team or Own scope)
- `visibleIds = []` — no visible records

The `team` scope uses `TeamMembershipChecker::visibleUserIds()` which returns user IDs from the user's direct teams **plus all descendant teams** via recursive CTE. This means a member of "Engineering" also sees users from "Backend", "Frontend", and all sub-teams. Implementation details are in the [Team module](../Team/README.md).

**Enforcement:** Controllers are blocked from doing scope resolution by three architecture rules:
- `NoScopeResolutionInPresentationRule` — blocks `canWithScope()` calls in Presentation
- `testPresentationDoesNotDependOnTeamMembershipChecker` — blocks importing the service
- `testPresentationDoesNotDependOnAccessContext` — blocks importing scope types

## Role Assignment (Superset Enforcement)

Users can assign roles to other users via the user edit form, gated by the `users.roles` feature (`read` and `update` actions).

**Superset rule**: A user can only assign a role if their effective permissions are a superset of that role's permissions (per leaf key, scope-aware). System roles additionally require super-admin status.

The `RoleAssignmentPolicy` domain service implements this check:

```php
$policy = new RoleAssignmentPolicy();
$assignable = $policy->assignableRoles($assignerPermissions, $candidateRoles, $modules, $isSuperAdmin);
```

## Per-User Overrides

Overrides allow granting or denying specific permissions per user, independent of their roles.

- **Grant override**: Adds a permission the user's roles don't provide
- **Deny override**: Blocks a permission even if roles grant it (deny always wins)

In the UI, denied permissions show a warning indicator explaining the override source.

## Impersonation

Super admins can impersonate any user to see the application from their perspective.

**Flow:**
1. Super admin clicks "Impersonate" on a user → `POST /impersonate/{userId}`
2. An impersonation session is created with a token
3. The `AuthenticatedUser` contract returns the impersonated user's ID
4. All permission checks use the impersonated user's permissions (no super-admin bypass)
5. Audit log records both the real and impersonated user IDs
6. `POST /stop-impersonation` ends the session

**Session lookup**: `SessionImpersonationManager` checks the `X-Impersonate-Token` header first (API), then falls back to the authenticated guard ID (web).

**Security:** Only users with a system role (super admin) can impersonate. The impersonated user's permissions are used — the impersonator does NOT retain their super-admin powers.

## Testing

`WithPermissions` trait is loaded automatically for all Feature tests via `Pest.php`:

- `$this->seedSuperAdminRole()` — creates the system super admin role
- `$this->assignSuperAdmin($userId)` — grants all permissions
- `$this->seedRoleWithPermissions($name, $desc, $perms)` — custom role
- `$this->assignRole($userId, $roleId)` — assigns any role

## Event Handlers

Domain event handlers in `App\Domain\Authorization\EventHandler\` react to authorization changes and refresh the cached authorization state via the `AuthorizationRefresher` contract:

| Handler | Event | Logic |
|---|---|---|
| `RefreshAuthorizationOnRoleAssigned` | `RoleAssignedToUser` | Refresh for the user |
| `RefreshAuthorizationOnRoleRevoked` | `RoleRevokedFromUser` | Refresh for the user |
| `RefreshAuthorizationOnOverrideSet` | `PermissionOverrideSet` | Refresh for the user |
| `RefreshAuthorizationOnOverrideRemoved` | `PermissionOverrideRemoved` | Refresh for the user |
| `RefreshAuthorizationOnRoleDeleted` | `RoleDeleted` | Refresh for all users with the role |
| `RefreshAuthorizationOnRoleUpdated` | `RoleUpdated` | Refresh for all users with the role |

The `AuthorizationRefresher` contract abstracts the refresh mechanism. The infrastructure implementation (`CacheAuthorizationRefresher`) increments the `auth:version:{userId}` counter, which invalidates all versioned cache keys for that user.

## Caching

- Permission checks are cached per user per permission with key `{tenant}:auth:can:{userId}:{permission}:v{version}`
- Scope decisions are cached with key `{tenant}:auth:scope:{userId}:{permission}:v{version}`
- Resource shares are cached with key `{tenant}:auth:shares:{userId}:{resourceType}:{action}:v{version}`
- TTL: 5 minutes (configurable via `AUTH_PERMISSION_CACHE_TTL` env var)
- Cache is automatically invalidated by domain event handlers via version increment (see above)

To debug cache issues, increment the auth version to invalidate all cached entries for a user:
```bash
php artisan tinker --execute="cache()->increment('{tenant}:auth:version:{userId}')"
```

## Database Tables

| Table | Purpose |
|-------|---------|
| `roles` | Role definitions (per-tenant + system) |
| `role_permissions` | Permissions assigned to roles |
| `user_roles` | Role-to-user assignments |
| `user_permission_overrides` | Per-user grant/deny overrides |
| `record_shares` | Record-level sharing |
| `permission_audit_log` | Audit trail for permission changes |
| `impersonation_sessions` | Active/completed impersonation sessions |

## Default Roles

When a new tenant is set up, these roles are seeded:

| Role | Read | Create | Update | Delete |
|------|------|--------|--------|--------|
| Manager | All | All | All | All |
| Team Leader | Team | Team | Team | Team |
| Team Member | All | Own | Own | — |
| Externist | Own | Own | Own | — |

Each permission can have its own scope (`All` > `Team` > `Own`), enabling granular access like "view everyone but only edit own records." The Super Admin role is a system role that bypasses all permission checks.
