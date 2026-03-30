<?php

declare(strict_types=1);

namespace App\Domain\Team;

interface TeamRepository
{
    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @return list<Team>
     */
    public function findAll(?array $onlyIds = null): array;

    public function findById(TeamId $teamId): ?Team;

    public function findBySlug(TeamSlug $teamSlug): ?Team;

    public function create(Team $team): void;

    public function update(Team $team): void;

    public function delete(TeamId $teamId): void;

    /**
     * @param  list<string>  $excludeTeamIds
     * @return list<Team>
     */
    public function search(string $term, array $excludeTeamIds, int $limit): array;

    public function count(): int;
}
