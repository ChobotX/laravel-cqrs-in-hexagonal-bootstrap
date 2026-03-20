<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Presentation\Http\Resource\RoleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class ListUserRolesController
{
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(string $userId): AnonymousResourceCollection
    {
        $organizationId = $this->organizationContext->currentOrganizationId();

        $roles = $this->queryBus->dispatch(
            new GetUserRolesQuery($userId, $organizationId),
        );

        return RoleResource::collection($roles);
    }
}
