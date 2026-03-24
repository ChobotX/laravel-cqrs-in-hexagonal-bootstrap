<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Team;

use App\Domain\Team\Team;
use App\Domain\Team\TeamId;
use App\Domain\Team\TeamName;
use App\Domain\Team\TeamSlug;

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
