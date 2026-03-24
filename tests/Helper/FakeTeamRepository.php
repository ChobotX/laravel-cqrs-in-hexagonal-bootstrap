<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Team\OrganizationId;
use App\Domain\Team\Team;
use App\Domain\Team\TeamId;
use App\Domain\Team\TeamRepository;
use App\Domain\Team\TeamSlug;

final class FakeTeamRepository implements TeamRepository
{
    /** @var list<Team> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, Team> $teams */
    public function __construct(
        private array $teams = [],
    ) {}

    /** @return list<Team> */
    public function findAllByOrganization(OrganizationId $organizationId): array
    {
        return array_values(array_filter(
            $this->teams,
            fn (Team $team): bool => $team->organizationId->value === $organizationId->value,
        ));
    }

    public function findById(TeamId $teamId): ?Team
    {
        return $this->teams[$teamId->value] ?? null;
    }

    public function findBySlugInOrganization(TeamSlug $teamSlug, OrganizationId $organizationId): ?Team
    {
        foreach ($this->teams as $team) {
            if ($team->slug->value === $teamSlug->value && $team->organizationId->value === $organizationId->value) {
                return $team;
            }
        }

        return null;
    }

    public function create(Team $team): void
    {
        $this->saved[] = $team;
        $this->teams[$team->id->value] = $team;
    }

    public function update(Team $team): void
    {
        $this->saved[] = $team;
        $this->teams[$team->id->value] = $team;
    }

    public function delete(TeamId $teamId): void
    {
        $this->deleted[] = $teamId->value;
        unset($this->teams[$teamId->value]);
    }
}
