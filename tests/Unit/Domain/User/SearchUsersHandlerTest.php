<?php

declare(strict_types=1);

use App\Application\Authorization\ScopeTarget;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\SearchUsersQuery;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Query\SearchUsersHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeUserRepository;

function searchUsersRepository(): FakeUserRepository
{
    return new FakeUserRepository([
        '550e8400-e29b-41d4-a716-446655440000' => new User(new UserId('550e8400-e29b-41d4-a716-446655440000'), new UserName('John Doe'), new Email('john@example.com')),
        '660e8400-e29b-41d4-a716-446655440000' => new User(new UserId('660e8400-e29b-41d4-a716-446655440000'), new UserName('Jane Smith'), new Email('jane@example.com')),
        '770e8400-e29b-41d4-a716-446655440000' => new User(new UserId('770e8400-e29b-41d4-a716-446655440000'), new UserName('Bob Builder'), new Email('bob@example.com')),
        '880e8400-e29b-41d4-a716-446655440000' => new User(new UserId('880e8400-e29b-41d4-a716-446655440000'), new UserName('Ondřej Černý'), new Email('ondrej@example.com')),
    ]);
}

it('returns matching users by name', function (): void {
    $handler = new SearchUsersHandler(searchUsersRepository());

    $result = $handler->handle(new SearchUsersQuery('John', [], 10));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('John Doe');
});

it('returns matching users by email', function (): void {
    $handler = new SearchUsersHandler(searchUsersRepository());

    $result = $handler->handle(new SearchUsersQuery('jane@', [], 10));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Jane Smith');
});

it('respects exclude user ids', function (): void {
    $handler = new SearchUsersHandler(searchUsersRepository());

    $result = $handler->handle(new SearchUsersQuery('example.com', ['550e8400-e29b-41d4-a716-446655440000'], 10));

    expect($result)->toHaveCount(3)
        ->and($result[0]->name->value)->toBe('Jane Smith')
        ->and($result[1]->name->value)->toBe('Bob Builder')
        ->and($result[2]->name->value)->toBe('Ondřej Černý');
});

it('respects limit', function (): void {
    $handler = new SearchUsersHandler(searchUsersRepository());

    $result = $handler->handle(new SearchUsersQuery('example.com', [], 2));

    expect($result)->toHaveCount(2);
});

it('returns empty when no matches', function (): void {
    $handler = new SearchUsersHandler(searchUsersRepository());

    $result = $handler->handle(new SearchUsersQuery('nonexistent', [], 10));

    expect($result)->toBeEmpty();
});

it('matches ignoring diacritics', function (): void {
    $handler = new SearchUsersHandler(searchUsersRepository());

    $result = $handler->handle(new SearchUsersQuery('cerny', [], 10));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Ondřej Černý');
});

it('matches diacritics term against ascii name', function (): void {
    $handler = new SearchUsersHandler(searchUsersRepository());

    $result = $handler->handle(new SearchUsersQuery('Černý', [], 10));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Ondřej Černý');
});

it('returns User scope target', function (): void {
    $query = new SearchUsersQuery('test', []);

    expect($query->scopeTarget())->toBe(ScopeTarget::User);
});
