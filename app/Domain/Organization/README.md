# Organization Module

The Organization bounded context manages multi-tenancy. Every authorization entity (roles, user_roles, permission_overrides) is scoped to an organization.

## Domain Model

| Class | Type | Description |
|---|---|---|
| `Organization` | Aggregate | `id`, `name`, `slug`, `description`. Soft-deleted. |
| `OrganizationId` | Value Object | UUID, validated on construction |
| `OrganizationName` | Value Object | Non-empty trimmed string |
| `OrganizationSlug` | Value Object | `[a-z0-9-]`, 2–63 chars, DNS-subdomain-safe |
| `OrganizationMember` | Read Model | `userId`, `organizationId`, `userName`, `userEmail`, `joinedAt` |

## Commands

| Command | Permission | Description |
|---|---|---|
| `CreateOrganization` | `organizations.management.create` | Validates slug uniqueness |
| `UpdateOrganization` | `organizations.management.update` | Validates slug uniqueness (excluding self) |
| `DeleteOrganization` | `organizations.management.delete` | Soft-deletes |
| `AddMember` | `organizations.members.update` | Validates user exists (cross-domain via QueryBus), rejects duplicates |
| `RemoveMember` | `organizations.members.update` | Cascades: revokes user_roles, permission_overrides, and team_members for that org |

## Queries

| Query | Permission | Description |
|---|---|---|
| `ListOrganizations` | `organizations.management.read` | Returns all organizations |
| `GetOrganizationById` | `organizations.management.read` | Throws `OrganizationNotFoundException` if missing |
| `GetUserOrganizations` | `#[SkipPermissionCheck]` | Returns orgs the user is a member of (used for context resolution) |
| `ListOrganizationMembers` | `organizations.members.read` | Returns members of a given organization |

## Events

| Event | Trigger |
|---|---|
| `OrganizationCreated` | After `CreateOrganization` — triggers `SeedDefaultRolesCommand` via infrastructure event handler |
| `OrganizationUpdated` | After `UpdateOrganization` |
| `OrganizationDeleted` | After `DeleteOrganization` |
| `MemberAdded` | After `AddMember` |
| `MemberRemoved` | After `RemoveMember` |

## Context Resolution

`OrganizationContext::currentOrganizationId()` returns `string` (never null for authenticated users). `CookieOrganizationContext` resolves the current org via this fallback chain: `X-Organization-Id` header → `organization_id` cookie → first org from membership → `DEFAULT_ORGANIZATION_ID` config fallback. For multi-org users without explicit selection, the first org is auto-selected (no 403). Throws `RuntimeException` if an authenticated user has zero memberships and no default is configured.

## Membership

Explicit `organization_members` table. Users must be added to an org before they can be assigned roles within it. Removing a member cascades all role assignments, permission overrides, and team memberships for that org.

## Teams

Teams organize organization members into sub-groups with a tree hierarchy. Teams determine **what data** users see (scope filtering), not what actions they can perform — org-level roles determine actions.

### Domain Model

| Class | Type | Description |
|---|---|---|
| `Team` | Aggregate | `id`, `organizationId`, `name`, `slug`, `description`, `parentTeamId` (nullable). Soft-deleted. |
| `TeamId` | Value Object | UUID, validated on construction |
| `TeamName` | Value Object | Non-empty trimmed string |
| `TeamSlug` | Value Object | `[a-z0-9-]`, 2–63 chars. Unique per organization. |
| `TeamMember` | Read Model | `userId`, `teamId`, `userName`, `userEmail`, `joinedAt` |

### Hierarchy

Full tree via adjacency list (`parent_team_id`) + recursive CTEs for descendant resolution. Membership cascades DOWN: a member of "Engineering" implicitly sees data from "Backend", "Frontend", etc.

- **Cycle detection**: On create/update with `parent_team_id`, the repository walks descendants via CTE to reject circular references.
- **Deletion policy**: Deleting a parent team reparents its children to the grandparent (or root). This reparenting logic lives in the repository.

### Commands

| Command | Permission | Description |
|---|---|---|
| `CreateTeam` | `teams.management.create` | Validates org exists, slug uniqueness within org, parent team exists and belongs to same org |
| `UpdateTeam` | `teams.management.update` | Validates slug uniqueness (excluding self), parent org match, self-parent cycle detection |
| `DeleteTeam` | `teams.management.delete` | Reparents children, soft-deletes |
| `AddTeamMember` | `teams.members.update` | Validates team exists, user is org member, rejects duplicates |
| `RemoveTeamMember` | `teams.members.update` | Validates membership exists |

### Queries

| Query | Permission | Description |
|---|---|---|
| `ListTeams` | `teams.management.read` | All teams for a given organization (flat list with `parentTeamId` for tree rendering) |
| `GetTeamById` | `teams.management.read` | Single team, throws `TeamNotFoundException` if missing |
| `ListTeamMembers` | `teams.members.read` | Direct members of a specific team |
| `GetUserTeams` | `#[SkipPermissionCheck]` | Returns teams a user directly belongs to in a given org (for membership management UI) |

### Events

| Event | Trigger |
|---|---|
| `TeamCreated` | After `CreateTeam` |
| `TeamUpdated` | After `UpdateTeam` |
| `TeamDeleted` | After `DeleteTeam` |
| `TeamMemberAdded` | After `AddTeamMember` |
| `TeamMemberRemoved` | After `RemoveTeamMember` |

### Scope Filtering

The `TeamMembershipChecker` contract (in `App\Contract\Organization`) provides two methods:

- `isTeamMember(userId, teamId)` — direct membership check
- `memberTeamIds(userId, organizationId)` — returns direct team IDs + all descendant team IDs (via recursive CTE)

The `TeamMemberRepository` additionally provides `directMemberTeamIds()` which returns only directly assigned teams (used for membership management UI, not scope filtering).

The `AccessScope::Team` in the authorization system uses `memberTeamIds()` to determine which records a user with team-scoped permissions can see.

## Cross-domain Integration

- `SeedDefaultRolesOnOrganizationCreated` (infrastructure event handler) dispatches `SeedDefaultRolesCommand` from the Authorization domain when a new organization is created.
- `AddMemberHandler` validates user existence via `GetUserByIdQuery` through the QueryBus.
- `AddTeamMemberHandler` validates org membership via `OrganizationMemberRepository` (same domain, no cross-domain import).
- The Authorization domain accesses teams only through the `TeamMembershipChecker` contract — no direct imports from Organization domain.
