<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\QueryBus;
use App\Domain\Team\Contract\Query\GetTeamTreeGridQuery;
use App\Domain\Team\Contract\ValueObject\TeamTreeGridMember;
use App\Domain\Team\Contract\ValueObject\TeamTreeGridNode;
use App\Domain\Team\Contract\ValueObject\TeamTreeGridRoleLabel;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('teams.management.read')]
final readonly class GetTeamTreeController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(): JsonResponse
    {
        /** @var list<TeamTreeGridNode> $nodes */
        $nodes = $this->queryBus->dispatch(new GetTeamTreeGridQuery(
            actingUserId: $this->authenticatedUser->id() ?? '',
        ));

        $data = array_map($this->mapNode(...), $nodes);

        return new JsonResponse(['data' => $data]);
    }

    /** @return array<string, mixed> */
    private function mapNode(TeamTreeGridNode $teamTreeGridNode): array
    {
        return [
            'id' => $teamTreeGridNode->id,
            'parentId' => $teamTreeGridNode->parentId,
            'name' => $teamTreeGridNode->name,
            'slug' => $teamTreeGridNode->slug,
            'memberCount' => $teamTreeGridNode->memberCount,
            'members' => array_map(
                $this->mapMember(...),
                $teamTreeGridNode->members,
            ),
        ];
    }

    /** @return array<string, mixed> */
    private function mapMember(TeamTreeGridMember $teamTreeGridMember): array
    {
        return [
            'id' => $teamTreeGridMember->userId,
            'name' => $teamTreeGridMember->userName,
            'avatarUrl' => $teamTreeGridMember->avatarFileId !== null ? route('files.show', ['fileId' => $teamTreeGridMember->avatarFileId]) : null,
            'detailUrl' => route('users.edit', ['userId' => $teamTreeGridMember->userId]),
            'roles' => array_map(fn (TeamTreeGridRoleLabel $teamTreeGridRoleLabel): array => [
                'id' => $teamTreeGridRoleLabel->id,
                'name' => $teamTreeGridRoleLabel->name,
                'detailUrl' => route('roles.show', ['roleId' => $teamTreeGridRoleLabel->id]),
            ], $teamTreeGridMember->roles),
        ];
    }
}
