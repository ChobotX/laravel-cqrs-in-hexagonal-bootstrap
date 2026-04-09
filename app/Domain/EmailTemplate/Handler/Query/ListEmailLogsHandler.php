<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Query;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\EmailTemplate\Contract\Entity\EmailLog;
use App\Domain\EmailTemplate\Contract\Query\ListEmailLogsQuery;
use App\Domain\EmailTemplate\Contract\Repository\EmailLogRepository;

/** @implements QueryHandler<ListEmailLogsQuery, PaginatedResult<EmailLog>> */
final readonly class ListEmailLogsHandler implements QueryHandler
{
    public function __construct(
        private EmailLogRepository $emailLogRepository,
    ) {}

    /** @return PaginatedResult<EmailLog> */
    public function handle(Query $query): PaginatedResult
    {
        $pagination = $query->pagination() ?? new Pagination;

        $items = $this->emailLogRepository->findAll($pagination->perPage, $pagination->offset());
        $total = $this->emailLogRepository->countAll();

        return new PaginatedResult(
            items: $items,
            total: $total,
            pagination: $pagination,
        );
    }
}
