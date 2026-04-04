<?php

declare(strict_types=1);

use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Domain\Notification\Contract\Query\ListOwnNotifications\ListOwnNotificationsQuery;

it('can be constructed with defaults', function (): void {
    $query = new ListOwnNotificationsQuery(userId: '550e8400-e29b-41d4-a716-446655440000');

    expect($query->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($query->isRead)->toBeNull()
        ->and($query->pagination())->toBeNull()
        ->and($query->sorting())->toBe([]);
});

it('returns new instance with pagination', function (): void {
    $query = new ListOwnNotificationsQuery(userId: '550e8400-e29b-41d4-a716-446655440000');
    $listOwnNotificationsQuery = $query->withPagination(new Pagination(2, 10));

    expect($listOwnNotificationsQuery)->not->toBe($query)
        ->and($listOwnNotificationsQuery->pagination()?->page)->toBe(2)
        ->and($listOwnNotificationsQuery->pagination()?->perPage)->toBe(10)
        ->and($listOwnNotificationsQuery->userId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('returns new instance with sorting', function (): void {
    $query = new ListOwnNotificationsQuery(userId: '550e8400-e29b-41d4-a716-446655440000');
    $listOwnNotificationsQuery = $query->withSorting([new Sorting('created_at', SortDirection::Desc)]);

    expect($listOwnNotificationsQuery)->not->toBe($query)
        ->and($listOwnNotificationsQuery->sorting())->toHaveCount(1)
        ->and($listOwnNotificationsQuery->sorting()[0]->column)->toBe('created_at');
});
