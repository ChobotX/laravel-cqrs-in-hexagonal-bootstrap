# Authorization Module

Enterprise-grade authorization with hierarchical permissions, RBAC, per-user overrides, multi-tenant teams, and user impersonation.

## Architecture Overview

### Permission Hierarchy (4-tier)

```
Module (e.g. "users", "crm")
  └── Feature (e.g. "list", "contacts", "roles")
       └── Action (read | create | update | delete)
           └── Scope (all | team_tree | team | own)
```

Four access scopes: `All` > `TeamTree` > `Team` > `Own`. The `Own` scope implicitly includes shared resources — there is no separate `Shared` scope.

Higher-level grants cascade down:
- `users` grants everything under `users.*.*`
- `users.list` grants all actions under `users.list.*`

### Resolution Algorithm (deny-wins)

1. If user has super-admin role (system role) → granted with scope `All`

Modules `feature_flags` and `user_recovery` are excluded from default tenant roles (`Manager`, `Team Leader`, …) in `SeedDefaultRolesHandler` and `TenantSeeder`, so only the system super-admin role receives those permissions unless you assign them explicitly.
2. If impersonating → use impersonated user's permissions (no super-admin bypass)
3. Collect all role permissions for user (union of all assigned roles)
4. Apply permission inheritance (module → features → actions)
5. Merge with per-user overrides: **any explicit deny wins** over any grant
6. For scope: when multiple grants exist, most permissive scope wins (`All` > `TeamTree` > `Team` > `Own`)

### Key Contracts

- `AuthorizationChecker` — main entry point for permission checks
- `AuthenticatedUser` — provides current user context including impersonation
- `ImpersonationManager` — manages impersonation sessions
- `TeamMembershipChecker` — resolves team memberships for both direct-team and descendant-tree filtering (see [Team module](../Team/README.md))

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

The `AuthorizeAction` bus middleware (`App\Domain\Authorization\Middleware\AuthorizeAction`) reads this attribute and checks permissions before the handler runs.

## Skipping Authorization

For commands/queries that intentionally skip authorization (e.g., login, public endpoints):

```php
use App\Application\Authorization\SkipPermissionCheck;

#[SkipPermissionCheck(reason: 'Public authentication endpoint')]
final readonly class GetUserByEmailQuery implements Query { ... }
```

The `reason` parameter is required — forces a conscious decision.

## Record-Level Access (Scope Filtering)

Scope filtering is handled transparently by the `ResolveScopeFilter` bus middleware (`App\Domain\Authorization\Middleware\ResolveScopeFilter`). Controllers never resolve scope — they dispatch a `ScopeAwareQuery` and the middleware enriches it with an `AccessContext` before the handler runs.

**Flow:**

1. Controller dispatches `ListUsersQuery` (no scope info)
2. `AuthorizeAction` middleware checks binary access (can/deny)
3. `ResolveScopeFilter` middleware reads `#[RequiresPermission]`, calls `canWithScope()`, resolves visible IDs via `TeamMembershipChecker`, and (for `ShareableScopeQuery`) fetches shared resource IDs via `AuthorizationChecker::accessibleResourceIds()`. It returns a new query with an `AccessContext`.
4. Handler reads `accessContext()?->visibleIds` and passes to repository
5. Repository applies `WHERE IN (...)` at the SQL level

**`AccessContext` semantics:**
- `visibleIds = null` — unrestricted (All scope, no SQL filter)
- `visibleIds = ['id1', 'id2']` — restrict to these IDs (Team or Own scope)
- `visibleIds = []` — no visible records
- `sharedResourceIds` — resource IDs shared with the actor (populated only when the query implements `ShareableScopeQuery` and the scope is not `All`). Null otherwise.

The `team` scope uses direct membership resolution (`directVisibleUserIds()` / `directMemberTeamIds()`), so descendants are excluded.

The `team_tree` scope uses recursive membership resolution (`visibleUserIds()` / `memberTeamIds()`) which returns IDs from the user's direct teams **plus all descendant teams** via recursive CTE. This means a member of "Engineering" also sees users from "Backend", "Frontend", and all sub-teams. Implementation details are in the [Team module](../Team/README.md).

**Enforcement:** Controllers are blocked from doing scope resolution by three architecture rules:
- `NoScopeResolutionInPresentationRule` — blocks `canWithScope()` calls in Presentation
- `testPresentationDoesNotDependOnTeamMembershipChecker` — blocks importing the service
- `testPresentationDoesNotDependOnAccessContext` — blocks importing scope types

## Record Sharing

Any entity type can opt in to universal record sharing, an extension of the scope system rather than a parallel mechanism. A user can grant access to a specific record to any user they can see; the grantee then sees the record in list views alongside records they own/can-see by scope.

**Turning on sharing for a new entity type** (three steps, no bespoke handler logic):

1. Add `created_by_user_id UUID NOT NULL` to the entity's table (FK to `users.id`).
2. Make its list query implement both `ScopeAwareQuery` and `ShareableScopeQuery`:
   ```php
   public function shareableResourceType(): string { return 'entry'; }
   ```
3. `use ScopesOwnedQuery;` in the Eloquent repository; call `$this->applyScopeFilter($builder, $accessContext)` in list methods.
4. Register the resource type in `AuthorizationServiceProvider`:
   ```php
   $this->app->singleton(ShareableResourceRegistry::class, fn () => new ShareableResourceRegistry([
       'entry' => 'registry.entries',  // resource_type => permission prefix
   ]));
   ```
5. Make the entity's `*Deleted` event implement `App\Contract\Event\EntityDeleted` (`entityId()` + `entityType()`). The generic `CleanupSharesOnEntityDeleted` handler revokes all shares on delete automatically.

Once wired, `ResolveScopeFilter` automatically unions shared IDs into `AccessContext::$sharedResourceIds`, and `ScopesOwnedQuery::applyScopeFilter()` produces `WHERE (created_by_user_id IN (visibleIds) OR id IN (sharedIds))`.

**Commands, queries, and the universal share API:**

- `ShareRecordCommand(granteeUserId, resourceType, resourceId, actions, grantorUserId)` — grants a list of actions (typically `['read', 'update']`). Emits one `RecordShared` event per action.
- `RevokeRecordShareCommand(granteeUserId, resourceType, resourceId)` — revokes all actions for a grantee + resource. Emits `RecordShareRevoked`.
- `GetSharesForResourceQuery(resourceType, resourceId)` — returns a `list<RecordShare>` for the resource (used by the SharePanel UI).
- HTTP: `GET/POST /internal-api/shares/{resourceType}/{resourceId}` and `DELETE /internal-api/shares/{resourceType}/{resourceId}/{granteeUserId}`. Controllers live in `Presentation/Http/Controller/Web/Sharing/`.

**Authorization rules at the share API layer** (enforced inline; see the three sharing controllers):

- Viewing/sharing additionally requires `users.list.read` so the actor can resolve grantee identities in the UI.
- Sharing requires `AuthorizationChecker::canShareResource($userId, $resourceType)` (update permission on the resource type).
- Revoking requires being the original grantor OR having `canShareResource()` (e.g. an admin can revoke anyone's share).

**`AuthorizationChecker` sharing methods:**

- `supportsResourceSharing(resourceType): bool` — whether the resource type is registered for sharing.
- `canShareResource(userId, resourceType): bool` — resolves the update permission for that type via the registry.
- `canViewResourceShares(userId, resourceType): bool` — resolves the read permission for that type.
- `accessibleResourceIds(userId, resourceType, action): list<string>` — shared record IDs visible to the user for that action.

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

## Bus Middleware

Authorization middleware lives in `App\Domain\Authorization\Middleware\` because it IS authorization business logic, not shared infrastructure plumbing. Enforced by PHPStan rule `testMiddlewareDoesNotLiveInInfrastructure`.

- `AuthorizeAction` — checks `#[RequiresPermission]` attribute before command/query execution. Fast-fails if the user lacks the required permission.
- `ResolveScopeFilter` — enriches `ScopeAwareQuery` instances with `AccessContext` based on the user's scope-level permission. Resolves visible IDs via `TeamMembershipChecker`.

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
| `RefreshAuthorizationOnRecordShared` | `RecordShared` | Refresh for the grantee user so their shared-resource cache invalidates |
| `RefreshAuthorizationOnRecordShareRevoked` | `RecordShareRevoked` | Refresh for the grantee user |
| `CleanupSharesOnEntityDeleted` | any `EntityDeleted` | Revoke all record shares for the deleted entity |

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
