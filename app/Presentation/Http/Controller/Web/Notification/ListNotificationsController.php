<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Notification;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Application\Pagination\Pagination;
use App\Contract\Auth\AuthenticatedUser;
use App\Domain\Notification\Contract\Entity\Notification;
use App\Domain\Notification\Contract\Query\ListOwnNotificationsQuery;
use App\Presentation\Http\Request\Web\Notification\ListNotificationsRequest;
use App\Presentation\Http\Request\Web\Notification\NotificationFilter;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck(reason: 'Notifications are accessible to all authenticated users')]
final readonly class ListNotificationsController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(ListNotificationsRequest $listNotificationsRequest): JsonResponse
    {
        $userId = $this->authenticatedUser->id() ?? '';
        $filter = $listNotificationsRequest->filter();
        $isRead = match ($filter) {
            NotificationFilter::Unread->value => false,
            NotificationFilter::Read->value => true,
            default => null,
        };

        $paginatedResult = $this->queryBus->dispatch(new ListOwnNotificationsQuery(
            userId: $userId,
            isRead: $isRead,
            pagination: new Pagination($listNotificationsRequest->page(), $listNotificationsRequest->perPage()),
        ));

        return new JsonResponse([
            'data' => array_map(static fn (Notification $notification): array => [
                'id' => $notification->id->value,
                'level' => $notification->level->value,
                'title' => $notification->title,
                'body' => $notification->body,
                'link_url' => $notification->link?->value,
                'read_at' => $notification->readAt?->format('c'),
                'created_at' => $notification->createdAt->format('c'),
            ], $paginatedResult->items),
            'meta' => [
                'current_page' => $paginatedResult->pagination->page,
                'per_page' => $paginatedResult->pagination->perPage,
                'total' => $paginatedResult->total,
                'total_pages' => $paginatedResult->totalPages(),
            ],
        ]);
    }
}
