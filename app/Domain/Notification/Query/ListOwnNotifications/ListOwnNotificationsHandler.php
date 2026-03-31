<?php

declare(strict_types=1);

namespace App\Domain\Notification\Query\ListOwnNotifications;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Notification\Notification;
use App\Domain\Notification\NotificationRepository;

/** @implements QueryHandler<ListOwnNotificationsQuery, PaginatedResult<Notification>> */
final readonly class ListOwnNotificationsHandler implements QueryHandler
{
    public function __construct(
        private NotificationRepository $notificationRepository,
    ) {}

    /** @return PaginatedResult<Notification> */
    public function handle(Query $query): PaginatedResult
    {
        $pagination = $query->pagination() ?? new Pagination(1, Pagination::DEFAULT_PER_PAGE);
        $sortings = $query->sorting() !== [] ? $query->sorting() : [new Sorting('created_at', SortDirection::Desc)];

        return $this->notificationRepository->findByRecipient(
            $query->userId,
            $pagination,
            $query->isRead,
            $sortings,
        );
    }
}
