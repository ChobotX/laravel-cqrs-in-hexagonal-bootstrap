<?php

declare(strict_types=1);

namespace App\Domain\Notification\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Notification\Contract\Query\CountUnreadNotificationsQuery;
use App\Domain\Notification\Contract\Repository\NotificationRepository;

/** @implements QueryHandler<CountUnreadNotificationsQuery, int> */
final readonly class CountUnreadNotificationsHandler implements QueryHandler
{
    public function __construct(
        private NotificationRepository $notificationRepository,
    ) {}

    public function handle(Query $query): int
    {
        return $this->notificationRepository->countUnreadByRecipient($query->userId);
    }
}
