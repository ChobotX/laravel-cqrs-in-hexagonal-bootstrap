<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamName;
use App\Domain\Organization\TeamSlug;

final readonly class TeamMapper
{
    public function toDomain(TeamModel $teamModel): Team
    {
        return new Team(
            id: new TeamId($teamModel->id),
            organizationId: new OrganizationId($teamModel->organization_id),
            name: new TeamName($teamModel->name),
            slug: new TeamSlug($teamModel->slug),
            description: $teamModel->description,
            parentTeamId: $teamModel->parent_team_id !== null ? new TeamId($teamModel->parent_team_id) : null,
        );
    }
}
