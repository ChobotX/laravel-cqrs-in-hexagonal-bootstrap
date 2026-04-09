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
 * @implements Query<PaginatedResult<EmailLog>>
 */
#[RequiresPermission('email_templates.logs.read')]
final readonly class ListEmailLogsQuery implements PaginableQuery, Query
{
    public function __construct(
        public ?string $templateType = null,
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
