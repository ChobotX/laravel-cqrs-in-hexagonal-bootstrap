<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\Query\GetAssignableRolesQuery;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RolePermission;
use App\Domain\Authorization\Handler\Query\GetAssignableRolesHandler;
use App\Domain\Authorization\Policy\RoleAssignmentPolicy;
use App\Domain\Authorization\Service\PermissionResolver;
use App\Domain\Authorization\ValueObject\Module;
use App\Domain\Authorization\ValueObject\PermissionKey;
use App\Domain\Authorization\ValueObject\RoleName;
use Tests\Helper\FakeRoleRepository;
use Tests\Helper\FakeUserPermissionRepository;

/** @return array<string, array{features: array<string, array{actions: list<string>}>}> */
function assignableRolesModules(): array
{
    return [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create', 'update', 'delete']],
            ],
        ],
    ];
}

it('returns assignable roles for a user with matching permissions', function (): void {
    $assignerRole = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Manager'),
        'Manager role',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::All)],
    );

    $candidateRole = new Role(
        new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Editor role',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::Team)],
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['assigner-1'] = [$assignerRole];

    $roleRepo = new FakeRoleRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $assignerRole,
        '660e8400-e29b-41d4-a716-446655440000' => $candidateRole,
    ]);

    $handler = new GetAssignableRolesHandler(
        $userPermRepo,
        $roleRepo,
        new PermissionResolver,
        new RoleAssignmentPolicy,
        assignableRolesModules(),
    );

    $result = $handler->handle(new GetAssignableRolesQuery('assigner-1'));

    expect($result)->toHaveCount(2);
});

it('returns all roles including system roles for super admin', function (): void {
    $superAdminRole = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Super Admin'),
        'System role',
        true,
        [],
    );

    $systemRole = new Role(
        new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Another System'),
        'Another system role',
        true,
        [],
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['super-admin-1'] = [$superAdminRole];

    $roleRepo = new FakeRoleRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $superAdminRole,
        '660e8400-e29b-41d4-a716-446655440000' => $systemRole,
    ]);

    $handler = new GetAssignableRolesHandler(
        $userPermRepo,
        $roleRepo,
        new PermissionResolver,
        new RoleAssignmentPolicy,
        assignableRolesModules(),
    );

    $result = $handler->handle(new GetAssignableRolesQuery('super-admin-1'));

    expect($result)->toHaveCount(2);
});

it('filters out roles with wider scope than assigner permissions', function (): void {
    $assignerRole = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Team Lead'),
        'Team-scoped role',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::Team)],
    );

    $narrowerRole = new Role(
        new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Viewer'),
        'Own-scoped role',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::Own)],
    );

    $widerRole = new Role(
        new RoleId('770e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Admin'),
        'All-scoped role',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::All)],
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['team-lead-1'] = [$assignerRole];

    $roleRepo = new FakeRoleRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $assignerRole,
        '660e8400-e29b-41d4-a716-446655440000' => $narrowerRole,
        '770e8400-e29b-41d4-a716-446655440000' => $widerRole,
    ]);

    $handler = new GetAssignableRolesHandler(
        $userPermRepo,
        $roleRepo,
        new PermissionResolver,
        new RoleAssignmentPolicy,
        assignableRolesModules(),
    );

    $result = $handler->handle(new GetAssignableRolesQuery('team-lead-1'));

    $resultIds = array_map(fn (Role $role): string => $role->id->value, $result);

    expect($result)->toHaveCount(2)
        ->and($resultIds)->toContain('550e8400-e29b-41d4-a716-446655440000')
        ->and($resultIds)->toContain('660e8400-e29b-41d4-a716-446655440000')
        ->and($resultIds)->not->toContain('770e8400-e29b-41d4-a716-446655440000');
});

it('filters out system roles for non-super-admin', function (): void {
    $assignerRole = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Manager'),
        'Manager role',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::All)],
    );

    $systemRole = new Role(
        new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Super Admin'),
        'System role',
        true,
        [],
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['manager-1'] = [$assignerRole];

    $roleRepo = new FakeRoleRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $assignerRole,
        '660e8400-e29b-41d4-a716-446655440000' => $systemRole,
    ]);

    $handler = new GetAssignableRolesHandler(
        $userPermRepo,
        $roleRepo,
        new PermissionResolver,
        new RoleAssignmentPolicy,
        assignableRolesModules(),
    );

    $result = $handler->handle(new GetAssignableRolesQuery('manager-1'));

    $resultIds = array_map(fn (Role $role): string => $role->id->value, $result);

    expect($result)->toHaveCount(1)
        ->and($resultIds)->toContain('550e8400-e29b-41d4-a716-446655440000')
        ->and($resultIds)->not->toContain('660e8400-e29b-41d4-a716-446655440000');
});

it('returns empty when user has no permissions', function (): void {
    $candidateRole = new Role(
        new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Editor role',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::All)],
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $roleRepo = new FakeRoleRepository(['660e8400-e29b-41d4-a716-446655440000' => $candidateRole]);

    $handler = new GetAssignableRolesHandler(
        $userPermRepo,
        $roleRepo,
        new PermissionResolver,
        new RoleAssignmentPolicy,
        assignableRolesModules(),
    );

    $result = $handler->handle(new GetAssignableRolesQuery('no-perms-user'));

    expect($result)->toBeEmpty();
});
