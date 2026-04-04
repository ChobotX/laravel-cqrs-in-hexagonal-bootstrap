<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Contract\Query\GetUserOverridesQuery;
use App\Domain\Authorization\Contract\UserPermissionOverride;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class GetUserPermissionsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $userId): JsonResponse
    {
        $overrides = $this->queryBus->dispatch(
            new GetUserOverridesQuery($userId),
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
