<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\SearchRoles;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\Role;

/** @implements Query<list<Role>> */
#[RequiresPermission('users.roles.read')]
final readonly class SearchRolesQuery implements Query
{
    /**
     * @param  list<string>  $excludeRoleIds
     */
    public function __construct(
        public string $term,
        public array $excludeRoleIds,
    ) {}
}
