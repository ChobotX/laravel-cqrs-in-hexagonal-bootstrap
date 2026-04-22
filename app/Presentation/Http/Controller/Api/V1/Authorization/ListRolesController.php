<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\QueryBus;
use App\Domain\Authorization\Contract\Query\ListRolesQuery;
use App\Presentation\Http\Request\PaginationRequest;
use App\Presentation\Http\Resource\RoleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class ListRolesController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): AnonymousResourceCollection
    {
        $paginatedResult = $this->queryBus->dispatch(new ListRolesQuery($paginationRequest->pagination()));

        return RoleResource::collection($paginatedResult->items)->additional([
            'meta' => [
                'current_page' => $paginatedResult->pagination->page,
                'per_page' => $paginatedResult->pagination->perPage,
                'total' => $paginatedResult->total,
                'total_pages' => $paginatedResult->totalPages(),
            ],
        ]);
    }
}
