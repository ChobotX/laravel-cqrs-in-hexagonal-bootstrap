<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetRoleById;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleRepository;

/** @implements QueryHandler<GetRoleByIdQuery, Role> */
final readonly class GetRoleByIdHandler implements QueryHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
    ) {}

    public function handle(Query $query): Role
    {
        $role = $this->roleRepository->findById(new RoleId($query->id));

        if (! $role instanceof Role) {
            throw new RoleNotFoundException($query->id);
        }

        return $role;
    }
}
