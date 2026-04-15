# Team Module

Hierarchical team structure with membership management and scope filtering.

## Domain Model

- `Team` — aggregate with `TeamId`, `TeamName`, `TeamSlug`, optional `parentTeamId` (tree hierarchy), and description
- `TeamMember` — read model for team membership (userId, teamId, userName, userEmail, joinedAt)
- `TeamRepository` — `findAll(?onlyIds)`, `findById()`, `findBySlug()`, `create()`, `update()`, `delete()`
- `TeamTreeNode` — read model for tree view (team + members)
- `TeamMemberRepository` — `add()`, `remove()`, `isMember()`, `memberTeamIds()`, `directMemberTeamIds()`, `listMembers()`, `visibleUserIds()`, `directVisibleUserIds()`, `removeAllByUser()`

## Team Hierarchy

Teams form a tree via `parentTeamId`. A team with no parent is a root team.

`memberTeamIds()` returns the user's direct team IDs **plus all descendant team IDs** via recursive CTE. This powers `AccessScope::TeamTree` filtering — a member of "Engineering" also sees data from "Backend", "Frontend", and all sub-teams.

`directMemberTeamIds()` returns only directly assigned team IDs. This powers `AccessScope::Team` (direct-only scope).

`visibleUserIds()` returns all user IDs visible to a given user under team scope — members of the user's teams and all descendant teams, always including the user themselves. Implemented as a single recursive CTE query to avoid N+1 (previously required one `listMembers()` call per team).

`directVisibleUserIds()` returns only users from directly assigned teams (no descendant expansion), always including the user themselves.

## Commands

| Command | Permission | Description |
|---|---|---|
| `CreateTeamCommand` | `teams.list.create` | Creates a new team |
| `UpdateTeamCommand` | `teams.list.update` | Updates team name, slug, description, parent |
| `DeleteTeamCommand` | `teams.list.delete` | Soft-deletes a team |
| `AddTeamMemberCommand` | `teams.list.update` | Adds a user to a team |
| `RemoveTeamMemberCommand` | `teams.list.update` | Removes a user from a team |

## Queries

| Query | Permission | Description |
|---|---|---|
| `ListTeamsQuery` | `teams.management.read` | Lists teams, scope-filtered by user access |
| `GetTeamTreeQuery` | `teams.management.read` | Gets team tree with members, scope-filtered |
| `GetTeamByIdQuery` | `teams.list.read` | Gets a single team with members |
| `GetUserTeamsQuery` | `teams.list.read` | Gets teams for a specific user |
| `SearchTeamsQuery` | `teams.list.read` | Searches teams by name/slug |

## Slug Uniqueness

Team slugs are unique per tenant schema (enforced by database unique constraint). `TeamSlugAlreadyExistsException` is thrown on conflict.
