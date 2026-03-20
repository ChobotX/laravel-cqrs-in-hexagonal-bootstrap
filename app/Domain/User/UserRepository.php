<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    /** @return list<User> */
    public function all(): array;

    public function findById(UserId $userId): ?User;

    public function findByEmail(string $email): ?User;

    public function create(User $user): void;

    public function update(User $user): void;

    public function delete(UserId $userId): void;

    /**
     * @param  list<string>  $restrictToOrganizationIds  When non-empty, only users who are members of these orgs
     * @param  list<string>  $excludeUserIds  Users to exclude from results
     * @return list<User>
     */
    public function search(string $term, array $restrictToOrganizationIds, array $excludeUserIds, int $limit): array;
}
