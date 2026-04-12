<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Repository;

use App\Application\Filtering\Filter;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;

/**
 * Persistence port for team data in the Team context; implementations live in Infrastructure.
 */
interface TeamRepository
{
    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     * @return list<Team>
     *                    Loads a record or value object, or null when absent.
     */
    public function findAll(?array $onlyIds = null, array $sortings = [], array $filters = []): array;

    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     * @return PaginatedResult<Team>
     *                               Loads a record or value object, or null when absent.
     */
    public function findAllPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = [], array $filters = []): PaginatedResult;

    /** Loads a record or value object, or null when absent. */
    public function findById(TeamId $teamId): ?Team;

    /** Loads a record or value object, or null when absent. */
    public function findBySlug(TeamSlug $teamSlug): ?Team;

    /** Persists a new or updated aggregate row. */
    public function create(Team $team): void;

    /** Contract operation `update`; see infrastructure for behavior. */
    public function update(Team $team): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(TeamId $teamId): void;

    /**
     * @param  list<string>  $excludeTeamIds
     * @return list<Team>
     *                    Returns a filtered collection according to repository rules.
     */
    public function search(string $term, array $excludeTeamIds, int $limit): array;

    /** Returns the number of matching rows. */
    public function count(): int;

    /** @return list<string> */
    public function ancestorTeamIds(TeamId $teamId): array;
}
