<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\User;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\User\Contract\Query\ListUsers\ListUsersQuery;
use App\Presentation\Http\Request\PaginationRequest;
use App\Presentation\Http\Resource\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class ListUsersController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): AnonymousResourceCollection
    {
        $paginatedResult = $this->queryBus->dispatch(new ListUsersQuery($paginationRequest->pagination()));

        return UserResource::collection($paginatedResult->items)->additional([
            'meta' => [
                'current_page' => $paginatedResult->pagination->page,
                'per_page' => $paginatedResult->pagination->perPage,
                'total' => $paginatedResult->total,
                'total_pages' => $paginatedResult->totalPages(),
            ],
        ]);
    }
}
