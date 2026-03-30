<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;

interface UserRepository
{
    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @return list<User>
     */
    public function all(?array $onlyIds = null, array $sortings = []): array;

    /**
     * @param  list<string>|null  $onlyIds  null = all records (no scope filter)
     * @param  list<Sorting>  $sortings
     * @return PaginatedResult<User>
     */
    public function allPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = []): PaginatedResult;

    public function findById(UserId $userId): ?User;

    public function findByEmail(string $email): ?User;

    public function create(User $user): void;

    public function update(User $user): void;

    public function delete(UserId $userId): void;

    /**
     * @param  list<string>  $excludeUserIds  Users to exclude from results
     * @param  list<string>|null  $onlyIds  null = no scope filter
     * @return list<User>
     */
    public function search(string $term, array $excludeUserIds, int $limit, ?array $onlyIds = null): array;

    public function count(): int;
}
