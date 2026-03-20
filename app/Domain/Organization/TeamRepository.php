<?php

declare(strict_types=1);

namespace App\Domain\Organization;

interface TeamRepository
{
    /** @return list<Team> */
    public function findAllByOrganization(OrganizationId $organizationId): array;

    public function findById(TeamId $teamId): ?Team;

    public function findBySlugInOrganization(TeamSlug $teamSlug, OrganizationId $organizationId): ?Team;

    public function create(Team $team): void;

    public function update(Team $team): void;

    public function delete(TeamId $teamId): void;
}
