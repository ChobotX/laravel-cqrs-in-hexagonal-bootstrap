<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Application\Pagination\PaginableQuery;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Contract\Query\Query;
use App\Domain\EmailTemplate\Contract\Entity\EmailLog;

/**
 * Query for list email logs in the EmailTemplate bounded context; dispatched through the query bus.
 *
 * @implements Query<PaginatedResult<EmailLog>>
 */
#[RequiresPermission('email_templates.logs.read')]
final readonly class ListEmailLogsQuery implements PaginableQuery, Query
{
    public function __construct(
        /** Classifier string or type discriminator. */
        public ?string $templateType = null,
        /** Optional recipient identifier when absent. */
        public ?string $recipientId = null,
        private ?Pagination $pagination = null,
    ) {}

    public function withPagination(Pagination $pagination): static
    {
        return new self($this->templateType, $this->recipientId, $pagination);
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }
}
