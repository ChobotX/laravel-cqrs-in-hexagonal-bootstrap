<?php

declare(strict_types=1);

namespace App\Domain\Team;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;

interface TeamRepository
{
    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @return list<Team>
     */
    public function findAll(?array $onlyIds = null, array $sortings = []): array;

    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @return PaginatedResult<Team>
     */
    public function findAllPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = []): PaginatedResult;

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

    /** @return list<string> */
    public function ancestorTeamIds(TeamId $teamId): array;
}
