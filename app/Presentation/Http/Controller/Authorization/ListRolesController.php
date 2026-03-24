<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Presentation\Http\Resource\RoleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class ListRolesController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): AnonymousResourceCollection
    {
        $roles = $this->queryBus->dispatch(
            new ListRolesQuery,
        );

        return RoleResource::collection($roles);
    }
}
