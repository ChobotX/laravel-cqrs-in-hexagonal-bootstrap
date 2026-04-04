<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Query\GetRoleByIdQuery;
use App\Domain\Authorization\Contract\Repository\RoleRepository;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Exception\RoleNotFoundException;

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
