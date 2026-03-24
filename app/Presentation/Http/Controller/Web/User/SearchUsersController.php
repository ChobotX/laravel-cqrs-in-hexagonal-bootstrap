<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\User\Query\SearchUsers\SearchUsersQuery;
use App\Presentation\Http\Request\Web\User\SearchUsersRequest;
use App\Presentation\Http\Resource\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[RequiresPermission('users.list.read')]
final readonly class SearchUsersController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(SearchUsersRequest $searchUsersRequest): AnonymousResourceCollection
    {
        $term = $searchUsersRequest->searchTerm();

        $users = $this->queryBus->dispatch(new SearchUsersQuery(
            term: $term,
            excludeUserIds: $searchUsersRequest->excludeUserIds(),
            limit: $term === '' ? 50 : 10,
        ));

        return UserResource::collection($users);
    }
}
