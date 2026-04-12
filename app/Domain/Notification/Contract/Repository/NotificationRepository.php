<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Repository;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Domain\Notification\Contract\Entity\Notification;
use App\Domain\Notification\Contract\ValueObject\NotificationId;
use DateTimeImmutable;

/**
 * Persistence port for notification data in the Notification context; implementations live in Infrastructure.
 */
interface NotificationRepository
{
    /** Persists a new or updated aggregate row. */
    public function create(Notification $notification): void;

    /** Loads a record or value object, or null when absent. */
    public function findById(NotificationId $notificationId): ?Notification;

    /**
     * @param  list<Sorting>  $sortings
     * @return PaginatedResult<Notification>
     *                                       Loads a record or value object, or null when absent.
     */
    public function findByRecipient(
        string $recipientId,
        Pagination $pagination,
        ?bool $isRead = null,
        array $sortings = [],
    ): PaginatedResult;

    /** Contract operation `markAsRead`; see infrastructure for behavior. */
    public function markAsRead(NotificationId $notificationId, DateTimeImmutable $readAt): void;

    /** Contract operation `markAllAsReadForRecipient`; see infrastructure for behavior. */
    public function markAllAsReadForRecipient(string $recipientId, DateTimeImmutable $readAt): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(NotificationId $notificationId): void;

    /** Deletes or soft-deletes the targeted record. */
    public function deleteAllForRecipient(string $recipientId): void;

    /** Returns the number of matching rows. */
    public function countUnreadByRecipient(string $recipientId): int;
}
