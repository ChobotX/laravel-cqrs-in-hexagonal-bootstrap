<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Query\GetUserOverrides\GetUserOverridesQuery;
use App\Domain\Authorization\UserPermissionOverride;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class GetUserPermissionsController
{
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(string $userId): JsonResponse
    {
        $organizationId = $this->organizationContext->currentOrganizationId();

        $overrides = $this->queryBus->dispatch(
            new GetUserOverridesQuery($userId, $organizationId),
        );

        return new JsonResponse([
            'data' => array_map(
                static fn (UserPermissionOverride $userPermissionOverride): array => [
                    'permission' => (string) $userPermissionOverride->permissionKey,
                    'type' => $userPermissionOverride->type->value,
                    'scope' => $userPermissionOverride->scope->value,
                ],
                $overrides,
            ),
        ]);
    }
}
