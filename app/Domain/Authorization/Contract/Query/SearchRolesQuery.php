<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\Entity\Role;

/**
 * Query for search roles in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<list<Role>>
 */
#[RequiresPermission('users.roles.read')]
final readonly class SearchRolesQuery implements Query
{
    /**
     * @param  list<string>  $excludeRoleIds
     */
    public function __construct(
        /** Field `term` for this contract; see module docs for validation rules. */
        public string $term,
        /** List of stable identifiers for batch operations. */
        public array $excludeRoleIds,
    ) {}
}
