<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Query\SearchRolesQuery;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Handler\Query\SearchRolesHandler;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeRoleRepository;

function searchRolesRepository(): FakeRoleRepository
{
    return new FakeRoleRepository([
        '550e8400-e29b-41d4-a716-446655440000' => new Role(new RoleId('550e8400-e29b-41d4-a716-446655440000'), new RoleName('Editor'), 'Editor role', false, []),
        '660e8400-e29b-41d4-a716-446655440000' => new Role(new RoleId('660e8400-e29b-41d4-a716-446655440000'), new RoleName('Viewer'), 'Viewer role', false, []),
        '770e8400-e29b-41d4-a716-446655440000' => new Role(new RoleId('770e8400-e29b-41d4-a716-446655440000'), new RoleName('Editor Pro'), 'Pro role', false, []),
    ]);
}

it('searches roles by name', function (): void {
    $handler = new SearchRolesHandler(searchRolesRepository());

    $result = $handler->handle(new SearchRolesQuery('Editor', []));

    expect($result)->toHaveCount(2)
        ->and($result[0]->name->value)->toBe('Editor')
        ->and($result[1]->name->value)->toBe('Editor Pro');
});

it('excludes specified role ids', function (): void {
    $handler = new SearchRolesHandler(searchRolesRepository());

    $result = $handler->handle(new SearchRolesQuery('Editor', ['550e8400-e29b-41d4-a716-446655440000']));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Editor Pro');
});

it('returns empty when no matches', function (): void {
    $handler = new SearchRolesHandler(searchRolesRepository());

    $result = $handler->handle(new SearchRolesQuery('nonexistent', []));

    expect($result)->toBeEmpty();
});
