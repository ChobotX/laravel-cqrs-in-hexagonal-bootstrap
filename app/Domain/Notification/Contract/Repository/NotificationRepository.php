<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Repository;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Domain\Notification\Contract\Entity\Notification;
use App\Domain\Notification\Contract\ValueObject\NotificationId;
use DateTimeImmutable;

interface NotificationRepository
{
    public function create(Notification $notification): void;

    public function findById(NotificationId $notificationId): ?Notification;

    /**
     * @param  list<Sorting>  $sortings
     * @return PaginatedResult<Notification>
     */
    public function findByRecipient(
        string $recipientId,
        Pagination $pagination,
        ?bool $isRead = null,
        array $sortings = [],
    ): PaginatedResult;

    public function markAsRead(NotificationId $notificationId, DateTimeImmutable $readAt): void;

    public function markAllAsReadForRecipient(string $recipientId, DateTimeImmutable $readAt): void;

    public function delete(NotificationId $notificationId): void;

    public function deleteAllForRecipient(string $recipientId): void;

    public function countUnreadByRecipient(string $recipientId): int;
}
