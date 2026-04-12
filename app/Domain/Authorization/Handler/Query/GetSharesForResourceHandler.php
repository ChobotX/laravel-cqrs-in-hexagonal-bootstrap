<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Query\GetSharesForResourceQuery;
use App\Domain\Authorization\Contract\Repository\RecordShareRepository;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;

/** @implements QueryHandler<GetSharesForResourceQuery, list<RecordShare>> */
final readonly class GetSharesForResourceHandler implements QueryHandler
{
    public function __construct(
        private RecordShareRepository $recordShareRepository,
    ) {}

    /** @return list<RecordShare> */
    public function handle(Query $query): array
    {
        return $this->recordShareRepository->findByResource(
            $query->resourceType,
            $query->resourceId,
        );
    }
}
