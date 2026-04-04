<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Contract\Query\GetRoleByIdQuery;
use App\Presentation\Http\Resource\RoleResource;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class GetRoleController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $roleId): RoleResource
    {
        $role = $this->queryBus->dispatch(new GetRoleByIdQuery($roleId));

        return new RoleResource($role);
    }
}
