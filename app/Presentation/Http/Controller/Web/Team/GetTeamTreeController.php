<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Query\GetRolesForUsersQuery;
use App\Domain\Team\Contract\Query\GetTeamTreeQuery;
use App\Domain\Team\Contract\ValueObject\TeamMember;
use App\Domain\Team\Contract\ValueObject\TeamTreeNode;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('teams.management.read')]
final readonly class GetTeamTreeController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(): JsonResponse
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        /** @var list<TeamTreeNode> $treeNodes */
        $treeNodes = $this->queryBus->dispatch(new GetTeamTreeQuery);

        $visibleTeamIds = $this->extractVisibleTeamIds($treeNodes);
        $roleMap = $this->buildRoleMap($treeNodes, $currentUserId);

        $data = array_map(
            fn (TeamTreeNode $teamTreeNode): array => $this->mapNode($teamTreeNode, $visibleTeamIds, $roleMap),
            $treeNodes,
        );

        return new JsonResponse(['data' => $data]);
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
    private function buildRoleMap(array $nodes, string $currentUserId): array
    {
        if (! $this->authorizationChecker->can($currentUserId, 'users.roles.read')) {
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
     * @return array<string, mixed>
     */
    private function mapNode(TeamTreeNode $teamTreeNode, array $visibleTeamIds, array $roleMap): array
    {
        $parentId = $teamTreeNode->team->parentTeamId instanceof \App\Domain\Team\Contract\ValueObject\TeamId
            ? $teamTreeNode->team->parentTeamId->value
            : '';

        if ($parentId !== '' && ! isset($visibleTeamIds[$parentId])) {
            $parentId = '';
        }

        return [
            'id' => $teamTreeNode->team->id->value,
            'parentId' => $parentId,
            'name' => $teamTreeNode->team->name->value,
            'slug' => $teamTreeNode->team->slug->value,
            'memberCount' => count($teamTreeNode->members),
            'members' => array_map(
                fn (TeamMember $teamMember): array => $this->mapMember($teamMember, $roleMap),
                $teamTreeNode->members,
            ),
        ];
    }

    /**
     * @param  array<string, list<Role>>  $roleMap
     * @return array<string, mixed>
     */
    private function mapMember(TeamMember $teamMember, array $roleMap): array
    {
        $roles = $roleMap[$teamMember->userId] ?? [];

        return [
            'id' => $teamMember->userId,
            'name' => $teamMember->userName,
            'avatarUrl' => $teamMember->avatarFileId !== null ? route('files.show', ['fileId' => $teamMember->avatarFileId]) : null,
            'detailUrl' => route('users.edit', ['userId' => $teamMember->userId]),
            'roles' => array_map(fn (Role $role): array => [
                'id' => $role->id->value,
                'name' => $role->name->value,
                'detailUrl' => route('roles.show', ['roleId' => $role->id->value]),
            ], $roles),
        ];
    }
}
