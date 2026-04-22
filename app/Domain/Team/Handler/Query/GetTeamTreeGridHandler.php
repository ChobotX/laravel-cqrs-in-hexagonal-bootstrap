<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Query;

use App\Contract\Auth\AuthorizationChecker;
use App\Contract\Bus\QueryBus;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Query\GetRolesForUsersQuery;
use App\Domain\Team\Contract\Query\GetTeamTreeGridQuery;
use App\Domain\Team\Contract\Query\GetTeamTreeQuery;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamMember;
use App\Domain\Team\Contract\ValueObject\TeamTreeGridMember;
use App\Domain\Team\Contract\ValueObject\TeamTreeGridNode;
use App\Domain\Team\Contract\ValueObject\TeamTreeGridRoleLabel;
use App\Domain\Team\Contract\ValueObject\TeamTreeNode;

/** @implements QueryHandler<GetTeamTreeGridQuery, list<TeamTreeGridNode>> */
final readonly class GetTeamTreeGridHandler implements QueryHandler
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function handle(Query $query): array
    {
        /** @var list<TeamTreeNode> $treeNodes */
        $treeNodes = $this->queryBus->dispatch(new GetTeamTreeQuery);

        $visibleTeamIds = $this->extractVisibleTeamIds($treeNodes);
        $roleMap = $this->buildRoleMap($treeNodes, $query->actingUserId);

        return array_map(
            fn (TeamTreeNode $teamTreeNode): TeamTreeGridNode => $this->mapNode($teamTreeNode, $visibleTeamIds, $roleMap),
            $treeNodes,
        );
    }

    /**
     * @param  list<TeamTreeNode>  $nodes
     * @return array<string, true>
     */
    private function extractVisibleTeamIds(array $nodes): array
    {
        $ids = [];

        foreach ($nodes as $node) {
            $ids[$node->team->id->value] = true;
        }

        return $ids;
    }

    /**
     * @param  list<TeamTreeNode>  $nodes
     * @return array<string, list<Role>>
     */
    private function buildRoleMap(array $nodes, string $actingUserId): array
    {
        if (! $this->authorizationChecker->can($actingUserId, 'users.roles.read')) {
            return [];
        }

        $userIds = [];

        foreach ($nodes as $node) {
            foreach ($node->members as $member) {
                $userIds[$member->userId] = true;
            }
        }

        $uniqueUserIds = array_keys($userIds);

        if ($uniqueUserIds === []) {
            return [];
        }

        return $this->queryBus->dispatch(new GetRolesForUsersQuery($uniqueUserIds));
    }

    /**
     * @param  array<string, true>  $visibleTeamIds
     * @param  array<string, list<Role>>  $roleMap
     */
    private function mapNode(TeamTreeNode $teamTreeNode, array $visibleTeamIds, array $roleMap): TeamTreeGridNode
    {
        $parentId = $teamTreeNode->team->parentTeamId instanceof TeamId
            ? $teamTreeNode->team->parentTeamId->value
            : '';

        if ($parentId !== '' && ! isset($visibleTeamIds[$parentId])) {
            $parentId = '';
        }

        return new TeamTreeGridNode(
            id: $teamTreeNode->team->id->value,
            parentId: $parentId,
            name: $teamTreeNode->team->name->value,
            slug: $teamTreeNode->team->slug->value,
            memberCount: count($teamTreeNode->members),
            members: array_map(
                fn (TeamMember $teamMember): TeamTreeGridMember => $this->mapMember($teamMember, $roleMap),
                $teamTreeNode->members,
            ),
        );
    }

    /**
     * @param  array<string, list<Role>>  $roleMap
     */
    private function mapMember(TeamMember $teamMember, array $roleMap): TeamTreeGridMember
    {
        $roles = $roleMap[$teamMember->userId] ?? [];

        return new TeamTreeGridMember(
            userId: $teamMember->userId,
            userName: $teamMember->userName,
            avatarFileId: $teamMember->avatarFileId,
            roles: array_map(
                fn (Role $role): TeamTreeGridRoleLabel => new TeamTreeGridRoleLabel(
                    id: $role->id->value,
                    name: $role->name->value,
                ),
                $roles,
            ),
        );
    }
}
