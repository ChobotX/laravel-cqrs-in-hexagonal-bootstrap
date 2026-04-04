<?php

declare(strict_types=1);

use App\Application\Authorization\ScopeTarget;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Domain\User\Contract\Query\ListUsers\ListUsersQuery;
use App\Domain\User\Contract\User;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Email;
use App\Domain\User\Query\ListUsers\ListUsersHandler;
use App\Domain\User\UserName;
use Tests\Helper\FakeUserRepository;

it('returns all users from the repository sorted by name', function (): void {
    $users = [
        '550e8400-e29b-41d4-a716-446655440000' => new User(new UserId('550e8400-e29b-41d4-a716-446655440000'), new UserName('John Doe'), new Email('john@example.com')),
        '660e8400-e29b-41d4-a716-446655440000' => new User(new UserId('660e8400-e29b-41d4-a716-446655440000'), new UserName('Jane Doe'), new Email('jane@example.com')),
    ];

    $repository = new FakeUserRepository($users);

    $handler = new ListUsersHandler($repository);

    $paginatedResult = $handler->handle(new ListUsersQuery);

    expect($paginatedResult)->toBeInstanceOf(PaginatedResult::class)
        ->and($paginatedResult->items)->toHaveCount(2)
        ->and($paginatedResult->items[0]->name->value)->toBe('Jane Doe')
        ->and($paginatedResult->items[1]->name->value)->toBe('John Doe')
        ->and($paginatedResult->total)->toBe(2);
});

it('returns an empty paginated result when no users exist', function (): void {
    $repository = new FakeUserRepository;

    $handler = new ListUsersHandler($repository);

    $paginatedResult = $handler->handle(new ListUsersQuery);

    expect($paginatedResult->items)->toBeEmpty()
        ->and($paginatedResult->total)->toBe(0);
});

it('paginates results when pagination is provided', function (): void {
    $users = [];
    for ($i = 1; $i <= 5; $i++) {
        $id = sprintf('550e8400-e29b-41d4-a716-44665544%04d', $i);
        $users[$id] = new User(new UserId($id), new UserName('User '.$i), new Email(sprintf('user%d@example.com', $i)));
    }

    $repository = new FakeUserRepository($users);
    $handler = new ListUsersHandler($repository);

    $paginatedResult = $handler->handle(new ListUsersQuery(new Pagination(2, 2)));

    expect($paginatedResult->items)->toHaveCount(2)
        ->and($paginatedResult->total)->toBe(5)
        ->and($paginatedResult->pagination->page)->toBe(2)
        ->and($paginatedResult->pagination->perPage)->toBe(2)
        ->and($paginatedResult->items[0]->name->value)->toBe('User 3')
        ->and($paginatedResult->items[1]->name->value)->toBe('User 4');
});

it('applies default sorting by name ascending', function (): void {
    $users = [
        '550e8400-e29b-41d4-a716-446655440000' => new User(new UserId('550e8400-e29b-41d4-a716-446655440000'), new UserName('Charlie'), new Email('charlie@example.com')),
        '660e8400-e29b-41d4-a716-446655440000' => new User(new UserId('660e8400-e29b-41d4-a716-446655440000'), new UserName('Alice'), new Email('alice@example.com')),
        '770e8400-e29b-41d4-a716-446655440000' => new User(new UserId('770e8400-e29b-41d4-a716-446655440000'), new UserName('Bob'), new Email('bob@example.com')),
    ];

    $handler = new ListUsersHandler(new FakeUserRepository($users));

    $paginatedResult = $handler->handle(new ListUsersQuery);

    expect($paginatedResult->items[0]->name->value)->toBe('Alice')
        ->and($paginatedResult->items[1]->name->value)->toBe('Bob')
        ->and($paginatedResult->items[2]->name->value)->toBe('Charlie');
});

it('applies explicit sorting', function (): void {
    $users = [
        '550e8400-e29b-41d4-a716-446655440000' => new User(new UserId('550e8400-e29b-41d4-a716-446655440000'), new UserName('Alice'), new Email('charlie@example.com')),
        '660e8400-e29b-41d4-a716-446655440000' => new User(new UserId('660e8400-e29b-41d4-a716-446655440000'), new UserName('Bob'), new Email('alice@example.com')),
    ];

    $handler = new ListUsersHandler(new FakeUserRepository($users));

    $paginatedResult = $handler->handle(new ListUsersQuery(sortings: [new Sorting('email', SortDirection::Asc)]));

    expect($paginatedResult->items[0]->email->value)->toBe('alice@example.com')
        ->and($paginatedResult->items[1]->email->value)->toBe('charlie@example.com');
});

it('supports withPagination immutable copy', function (): void {
    $query = new ListUsersQuery;
    $listUsersQuery = $query->withPagination(new Pagination(2, 10));

    expect($query->pagination())->toBeNull()
        ->and($listUsersQuery->pagination())->toBeInstanceOf(Pagination::class)
        ->and($listUsersQuery->pagination())->not->toBeNull()
        ->and($listUsersQuery->pagination()?->page)->toBe(2);
});

it('supports withSorting immutable copy', function (): void {
    $query = new ListUsersQuery;
    $listUsersQuery = $query->withSorting([new Sorting('email', SortDirection::Desc)]);

    expect($query->sorting())->toBe([])
        ->and($listUsersQuery->sorting())->toHaveCount(1)
        ->and($listUsersQuery->sorting()[0]->column)->toBe('email')
        ->and($listUsersQuery->sorting()[0]->direction)->toBe(SortDirection::Desc);
});

it('returns User scope target', function (): void {
    $query = new ListUsersQuery;

    expect($query->scopeTarget())->toBe(ScopeTarget::User);
});
