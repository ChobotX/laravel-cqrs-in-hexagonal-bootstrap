<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\User;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\User\Query\ListUsers\ListUsersQuery;
use App\Presentation\Http\Resource\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class ListUsersController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): AnonymousResourceCollection
    {
        $users = $this->queryBus->dispatch(new ListUsersQuery);

        return UserResource::collection($users);
    }
}
