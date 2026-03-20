<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use App\Domain\Organization\TeamMember;
use DateTimeImmutable;

final readonly class TeamMemberMapper
{
    public function toDomain(TeamMemberModel $teamMemberModel): TeamMember
    {
        $user = $teamMemberModel->user;

        return new TeamMember(
            userId: $teamMemberModel->user_id,
            teamId: $teamMemberModel->team_id,
            userName: $user !== null ? $user->name : '',
            userEmail: $user !== null ? $user->email : '',
            joinedAt: new DateTimeImmutable($teamMemberModel->joined_at),
        );
    }
}
