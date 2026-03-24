<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\Eloquent\User\UserMapper;
use App\Infrastructure\Eloquent\User\UserModel;

function searchRepo(): EloquentUserRepository
{
    return new EloquentUserRepository(new UserMapper);
}

function createSearchTestUser(string $id, string $name, string $email): UserModel
{
    return UserModel::create([
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'password' => 'hashed',
    ]);
}

it('searches by name substring', function (): void {
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440a00', 'Alice Wonderland', 'alice@test.com');
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440a01', 'Bob Marley', 'bob@test.com');

    $results = searchRepo()->search('Alice', [], 10);

    expect($results)->toHaveCount(1)
        ->and($results[0]->name)->toBe('Alice Wonderland');
});

it('searches by email substring', function (): void {
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440b00', 'Charlie Brown', 'charlie@acme.com');
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440b01', 'Dave Jones', 'dave@other.com');

    $results = searchRepo()->search('acme', [], 10);

    expect($results)->toHaveCount(1)
        ->and($results[0]->name)->toBe('Charlie Brown');
});

it('search is case-insensitive', function (): void {
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440c00', 'Eve Adams', 'eve@test.com');

    $results = searchRepo()->search('eve', [], 10);

    expect($results)->toHaveCount(1)
        ->and($results[0]->name)->toBe('Eve Adams');
});

it('excludes specified user ids', function (): void {
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440d00', 'Frank Test', 'frank@test.com');
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440d01', 'Grace Test', 'grace@test.com');

    $results = searchRepo()->search('Test', ['550e8400-e29b-41d4-a716-446655440d00'], 10);

    expect($results)->toHaveCount(1)
        ->and($results[0]->name)->toBe('Grace Test');
});

it('limits results', function (): void {
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440e00', 'Limit User One', 'limit1@test.com');
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440e01', 'Limit User Two', 'limit2@test.com');
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440e02', 'Limit User Three', 'limit3@test.com');

    $results = searchRepo()->search('Limit', [], 2);

    expect($results)->toHaveCount(2);
});

it('matches ignoring diacritics', function (): void {
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440200', 'Ondřej Černý', 'ondrej@test.com');

    $results = searchRepo()->search('cerny', [], 10);

    expect($results)->toHaveCount(1)
        ->and($results[0]->name)->toBe('Ondřej Černý');
});

it('matches diacritics term against diacritics name', function (): void {
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440201', 'Jiří Šťastný', 'jiri@test.com');

    $results = searchRepo()->search('stastny', [], 10);

    expect($results)->toHaveCount(1)
        ->and($results[0]->name)->toBe('Jiří Šťastný');
});

it('returns empty when no matches', function (): void {
    createSearchTestUser('550e8400-e29b-41d4-a716-446655440100', 'Nobody Match', 'nobody@test.com');

    $results = searchRepo()->search('zzzznonexistent', [], 10);

    expect($results)->toBeEmpty();
});
