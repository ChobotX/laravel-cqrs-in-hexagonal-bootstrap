<?php

declare(strict_types=1);

namespace App\Domain\Team;

interface TeamRepository
{
    /** @return list<Team> */
    public function findAll(): array;

    public function findById(TeamId $teamId): ?Team;

    public function findBySlug(TeamSlug $teamSlug): ?Team;

    public function create(Team $team): void;

    public function update(Team $team): void;

    public function delete(TeamId $teamId): void;
}
