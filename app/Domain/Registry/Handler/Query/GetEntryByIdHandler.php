<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Query\GetEntryByIdQuery;
use App\Domain\Registry\Contract\Repository\EntryRepository;
use App\Domain\Registry\Contract\ValueObject\EntryId;

/**
 * @implements QueryHandler<GetEntryByIdQuery, ?Entry>
 */
final readonly class GetEntryByIdHandler implements QueryHandler
{
    public function __construct(
        private EntryRepository $entryRepository,
    ) {}

    public function handle(Query $query): ?Entry
    {
        return $this->entryRepository->findById(new EntryId($query->id));
    }
}
