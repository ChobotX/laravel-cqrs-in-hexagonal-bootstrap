<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\User\Contract\Query\SearchUsersQuery;
use App\Presentation\Http\Request\Web\User\SearchUsersRequest;
use App\Presentation\Http\Resource\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[RequiresPermission('users.list.read')]
final readonly class SearchUsersController
{
    private const int BROWSE_LIMIT = 50;

    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(SearchUsersRequest $searchUsersRequest): AnonymousResourceCollection
    {
        $term = $searchUsersRequest->searchTerm();

        $users = $this->queryBus->dispatch(new SearchUsersQuery(
            term: $term,
            excludeUserIds: $searchUsersRequest->excludeUserIds(),
            limit: $term === '' ? self::BROWSE_LIMIT : SearchUsersQuery::DEFAULT_LIMIT,
        ));

        return UserResource::collection($users);
    }
}
