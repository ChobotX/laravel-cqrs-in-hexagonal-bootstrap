<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\User;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use App\Presentation\Http\Resource\UserResource;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class GetUserController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $userId): UserResource
    {
        $user = $this->queryBus->dispatch(new GetUserByIdQuery($userId));

        return new UserResource($user);
    }
}
