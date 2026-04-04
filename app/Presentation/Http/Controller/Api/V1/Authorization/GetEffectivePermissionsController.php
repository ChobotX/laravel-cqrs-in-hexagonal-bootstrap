<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Contract\Query\GetEffectivePermissionsQuery;
use App\Presentation\Http\Resource\EffectivePermissionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class GetEffectivePermissionsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $userId): AnonymousResourceCollection
    {
        $permissions = $this->queryBus->dispatch(
            new GetEffectivePermissionsQuery($userId),
        );

        return EffectivePermissionResource::collection($permissions);
    }
}
