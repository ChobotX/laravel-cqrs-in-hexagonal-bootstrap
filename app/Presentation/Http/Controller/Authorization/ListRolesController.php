<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Exception\PermissionDeniedException;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Presentation\Http\Resource\RoleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class ListRolesController
{
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(): AnonymousResourceCollection
    {
        $organizationId = $this->organizationContext->currentOrganizationId();

        if ($organizationId === null) {
            throw new PermissionDeniedException;
        }

        $roles = $this->queryBus->dispatch(
            new ListRolesQuery($organizationId),
        );

        return RoleResource::collection($roles);
    }
}
