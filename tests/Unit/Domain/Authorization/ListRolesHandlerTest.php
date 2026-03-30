<?php

declare(strict_types=1);

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Query\ListRoles\ListRolesHandler;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use App\Domain\Authorization\RolePermission;
use Tests\Helper\FakeRoleRepository;

it('returns all roles', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Editor role',
        false,
        [],
    );

    $roleRepo = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);

    $handler = new ListRolesHandler($roleRepo);

    $paginatedResult = $handler->handle(new ListRolesQuery);

    expect($paginatedResult)->toBeInstanceOf(PaginatedResult::class)
        ->and($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->items[0]->name->value)->toBe('Editor')
        ->and($paginatedResult->total)->toBe(1);
});

it('returns empty paginated result when no roles exist', function (): void {
    $roleRepo = new FakeRoleRepository;

    $handler = new ListRolesHandler($roleRepo);

    $paginatedResult = $handler->handle(new ListRolesQuery);

    expect($paginatedResult->items)->toBeEmpty()
        ->and($paginatedResult->total)->toBe(0);
});

it('paginates results when pagination is provided', function (): void {
    $roles = [];
    for ($i = 1; $i <= 3; $i++) {
        $id = sprintf('550e8400-e29b-41d4-a716-44665544%04d', $i);
        $roles[$id] = new Role(new RoleId($id), new RoleName('Role '.$i), 'Description', false, []);
    }

    $handler = new ListRolesHandler(new FakeRoleRepository($roles));

    $paginatedResult = $handler->handle(new ListRolesQuery(new Pagination(2, 1)));

    expect($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->total)->toBe(3)
        ->and($paginatedResult->pagination->page)->toBe(2);
});

it('sorts roles by permission score descending by default', function (): void {
    $lowPermRole = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440001'),
        new RoleName('Viewer'),
        'Low permissions',
        false,
        [new RolePermission(new PermissionKey(new Module('users'), new Feature('list'), Action::Read), AccessScope::Own)],
    );

    $highPermRole = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440002'),
        new RoleName('Admin'),
        'High permissions',
        false,
        [
            new RolePermission(new PermissionKey(new Module('users'), new Feature('list'), Action::Read), AccessScope::All),
            new RolePermission(new PermissionKey(new Module('users'), new Feature('list'), Action::Update), AccessScope::All),
        ],
    );

    $roleRepo = new FakeRoleRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $lowPermRole,
        '550e8400-e29b-41d4-a716-446655440002' => $highPermRole,
    ]);

    $handler = new ListRolesHandler($roleRepo);

    $result = $handler->handle(new ListRolesQuery);

    expect($result->items[0]->name->value)->toBe('Admin')
        ->and($result->items[1]->name->value)->toBe('Viewer');
});

it('sorts system roles to the top', function (): void {
    $systemRole = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440001'),
        new RoleName('Super Admin'),
        'System role',
        true,
        [],
    );

    $normalRole = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440002'),
        new RoleName('Editor'),
        'Normal role',
        false,
        [
            new RolePermission(new PermissionKey(new Module('users'), new Feature('list'), Action::Read), AccessScope::All),
            new RolePermission(new PermissionKey(new Module('users'), new Feature('list'), Action::Update), AccessScope::All),
        ],
    );

    $roleRepo = new FakeRoleRepository([
        '550e8400-e29b-41d4-a716-446655440002' => $normalRole,
        '550e8400-e29b-41d4-a716-446655440001' => $systemRole,
    ]);

    $handler = new ListRolesHandler($roleRepo);

    $result = $handler->handle(new ListRolesQuery);

    expect($result->items[0]->name->value)->toBe('Super Admin')
        ->and($result->items[1]->name->value)->toBe('Editor');
});

it('supports withPagination immutable copy', function (): void {
    $query = new ListRolesQuery;
    $listRolesQuery = $query->withPagination(new Pagination(2, 10));

    expect($query->pagination())->toBeNull()
        ->and($listRolesQuery->pagination())->toBeInstanceOf(Pagination::class)
        ->and($listRolesQuery->pagination())->not->toBeNull()
        ->and($listRolesQuery->pagination()?->page)->toBe(2);
});

it('supports withSorting immutable copy', function (): void {
    $query = new ListRolesQuery;
    $sorted = $query->withSorting([new Sorting('name', SortDirection::Asc)]);

    expect($query->sorting())->toBe([])
        ->and($sorted->sorting())->toHaveCount(1)
        ->and($sorted->sorting()[0]->column)->toBe('name')
        ->and($sorted->sorting()[0]->direction)->toBe(SortDirection::Asc);
});
