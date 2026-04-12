<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Query\GetRecordSharesQuery;
use App\Domain\Authorization\Contract\Repository\RecordShareRepository;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;

/** @implements QueryHandler<GetRecordSharesQuery, list<RecordShare>> */
final readonly class GetRecordSharesHandler implements QueryHandler
{
    public function __construct(
        private RecordShareRepository $recordShareRepository,
    ) {}

    /** @return list<RecordShare> */
    public function handle(Query $query): array
    {
        return $this->recordShareRepository->findByGrantee(
            $query->userId,
            $query->resourceType,
        );
    }
}
