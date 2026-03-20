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
3. Collect all role permissions for user within current org (union of all assigned roles)
4. Apply permission inheritance (module → features → actions)
5. Merge with per-user overrides: **any explicit deny wins** over any grant
6. For scope: when multiple grants exist, most permissive scope wins (`All` > `Team` > `Own`)

### Key Contracts

- `AuthorizationChecker` — main entry point for permission checks
- `AuthenticatedUser` — provides current user context including impersonation
- `ImpersonationManager` — manages impersonation sessions
- `OrganizationContext` — resolves current organization from request (see [Organization module](../Organization/README.md))
- `OrganizationMembershipChecker` — verifies user belongs to an organization (see [Organization module](../Organization/README.md))
- `TeamMembershipChecker` — resolves team memberships with descendant expansion for `AccessScope::Team` filtering (see [Organization module](../Organization/README.md))

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

Query handlers call `canWithScope()` to get the access scope, then filter:

```php
$decision = $checker->canWithScope($userId, $orgId, 'crm.contacts.read');

if (!$decision->granted()) {
    throw new PermissionDeniedException('crm.contacts.read');
}

match ($decision->scope()) {
    'all' => $query,                           // no filter
    'team' => $query->whereTeam($teamIds),     // filter by team (uses TeamMembershipChecker::memberTeamIds)
    'own' => $query->whereOwner($userId),      // filter by owner (includes shared)
};
```

The `team` scope uses `TeamMembershipChecker::memberTeamIds()` which returns the user's direct team IDs **plus all descendant team IDs** via recursive CTE. This means a member of "Engineering" also sees data from "Backend", "Frontend", and all sub-teams. Implementation details are in the [Organization module](../Organization/README.md#scope-filtering).

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
- `$this->seedRoleWithPermissions($orgId, $name, $desc, $perms)` — custom role
- `$this->assignRole($userId, $roleId, $orgId)` — assigns any role

## Caching

- Effective permissions are cached per user+org with key `auth:perms:{orgId}:{userId}`
- TTL: 5 minutes (configurable via `AUTH_PERMISSION_CACHE_TTL` env var)
- Cache is automatically invalidated when:
  - Role assigned/revoked to/from user
  - Permission override set/removed
  - Role updated or deleted (clears cache for all users with that role)

To debug cache issues, clear the authorization cache:
```bash
php artisan cache:forget "auth:perms:{orgId}:{userId}"
```

## Database Tables

| Table | Purpose |
|-------|---------|
| `roles` | Role definitions (per-org + system) |
| `role_permissions` | Permissions assigned to roles |
| `user_roles` | Role-to-user assignments per org |
| `user_permission_overrides` | Per-user grant/deny overrides |
| `record_shares` | Record-level sharing |
| `permission_audit_log` | Audit trail for permission changes |
| `impersonation_sessions` | Active/completed impersonation sessions |

## Default Roles

When a new organization is created, these roles are seeded:

| Role | Permissions | Scope |
|------|------------|-------|
| Admin | All modules (including `users.roles`) | All |
| Editor | Read, Create, Update (including `users.roles.read`, `users.roles.update`) | All |
| Viewer | Read only (including `users.roles.read`) | All |

The Super Admin role is a global system role (no org) that bypasses all permission checks.

## Nullable Organization ID on Role

The `Role` aggregate has a nullable `$organizationId` because system roles (super-admin) are org-agnostic. This is an intentional exception — most aggregates require a non-nullable `$organizationId` (enforced by `AggregateRequiresOrganizationIdRule`). `Role` uses the `#[AllowNullableOrganizationId]` attribute to opt out of this enforcement.
