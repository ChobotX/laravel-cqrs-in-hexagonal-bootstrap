# Team Module

Hierarchical team structure with membership management and scope filtering.

## Domain Model

- `Team` — aggregate with `TeamId`, `TeamName`, `TeamSlug`, optional `parentTeamId` (tree hierarchy), and description
- `TeamMember` — read model for team membership (userId, teamId, userName, userEmail, joinedAt)
- `TeamRepository` — `findAll(?onlyIds)`, `findById()`, `findBySlug()`, `create()`, `update()`, `delete()`
- `TeamTreeNode` — read model for tree view (team + members)
- `TeamMemberRepository` — `add()`, `remove()`, `isMember()`, `memberTeamIds()`, `directMemberTeamIds()`, `listMembers()`, `removeAllByUser()`

## Team Hierarchy

Teams form a tree via `parentTeamId`. A team with no parent is a root team.

`memberTeamIds()` returns the user's direct team IDs **plus all descendant team IDs** via recursive CTE. This powers `AccessScope::Team` filtering — a member of "Engineering" also sees data from "Backend", "Frontend", and all sub-teams.

`directMemberTeamIds()` returns only directly assigned team IDs (for membership management UI).

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
