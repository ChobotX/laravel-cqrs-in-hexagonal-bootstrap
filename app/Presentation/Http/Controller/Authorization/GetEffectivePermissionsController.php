<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Exception\PermissionDeniedException;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Presentation\Http\Resource\EffectivePermissionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class GetEffectivePermissionsController
{
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(string $userId): AnonymousResourceCollection
    {
        $organizationId = $this->organizationContext->currentOrganizationId();

        if ($organizationId === null) {
            throw new PermissionDeniedException;
        }

        $permissions = $this->queryBus->dispatch(
            new GetEffectivePermissionsQuery($userId, $organizationId),
        );

        return EffectivePermissionResource::collection($permissions);
    }
}
