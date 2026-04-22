<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

/**
 * Composed users-grid read model: rows + pagination meta + per-actor capability flags.
 */
final readonly class UsersGridResult
{
    /**
     * @param  list<UserGridRow>  $rows
     */
    public function __construct(
        public array $rows,
        public int $total,
        public int $page,
        public int $perPage,
        public int $totalPages,
        public UsersGridPermissions $permissions,
    ) {}
}
