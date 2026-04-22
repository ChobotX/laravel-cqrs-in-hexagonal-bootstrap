<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Query;

use App\Application\Pagination\PaginableQuery;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortableQuery;
use App\Application\Sorting\Sorting;
use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Notification\Contract\Entity\Notification;

/**
 * Query for list own notifications in the Notification bounded context; dispatched through the query bus.
 *
 * @implements Query<PaginatedResult<Notification>>
 */
#[SkipPermissionCheck(reason: 'Users view only their own notifications')]
final readonly class ListOwnNotificationsQuery implements PaginableQuery, Query, SortableQuery
{
    /**
     * @param  list<Sorting>  $sortings
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Boolean flag for this state or capability. */
        public ?bool $isRead = null,
        private ?Pagination $pagination = null,
        private array $sortings = [],
    ) {}

    public function withPagination(Pagination $pagination): static
    {
        return new self($this->userId, $this->isRead, $pagination, $this->sortings);
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }

    /** @param list<Sorting> $sortings */
    public function withSorting(array $sortings): static
    {
        return new self($this->userId, $this->isRead, $this->pagination, $sortings);
    }

    /** @return list<Sorting> */
    public function sorting(): array
    {
        return $this->sortings;
    }
}
