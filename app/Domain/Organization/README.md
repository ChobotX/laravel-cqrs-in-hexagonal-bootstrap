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
| `RemoveMember` | `organizations.members.update` | Cascades: revokes user_roles + permission_overrides for that org |

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

`CookieOrganizationContext` resolves the current org: encrypted cookie → `X-Organization-Id` header → auto-select for single-org users → `DEFAULT_ORGANIZATION_ID` config fallback.

## Membership

Explicit `organization_members` table. Users must be added to an org before they can be assigned roles within it. Removing a member cascades all role assignments and permission overrides for that org.

## Cross-domain Integration

- `SeedDefaultRolesOnOrganizationCreated` (infrastructure event handler) dispatches `SeedDefaultRolesCommand` from the Authorization domain when a new organization is created.
- `AddMemberHandler` validates user existence via `GetUserByIdQuery` through the QueryBus.
