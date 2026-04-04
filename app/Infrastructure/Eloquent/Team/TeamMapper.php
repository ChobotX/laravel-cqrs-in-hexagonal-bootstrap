<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Team;

use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamSlug;
use App\Domain\Team\TeamName;

final readonly class TeamMapper
{
    public function toDomain(TeamModel $teamModel): Team
    {
        return new Team(
            id: new TeamId($teamModel->id),
            name: new TeamName($teamModel->name),
            slug: new TeamSlug($teamModel->slug),
            description: $teamModel->description,
            parentTeamId: $teamModel->parent_team_id !== null ? new TeamId($teamModel->parent_team_id) : null,
        );
    }
}
