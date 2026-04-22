<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\QueryBus;
use App\Domain\Authorization\Contract\Query\GetUserRolesQuery;
use App\Presentation\Http\Resource\RoleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class ListUserRolesController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $userId): AnonymousResourceCollection
    {
        $roles = $this->queryBus->dispatch(
            new GetUserRolesQuery($userId),
        );

        return RoleResource::collection($roles);
    }
}
