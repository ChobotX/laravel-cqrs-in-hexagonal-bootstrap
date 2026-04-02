<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Query\GetRolesForUsers\GetRolesForUsersHandler;
use App\Domain\Authorization\Query\GetRolesForUsers\GetRolesForUsersQuery;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeUserPermissionRepository;

it('returns roles grouped by user id', function (): void {
    $role = new Role(new RoleId('00000000-0000-0000-0000-00000000000a'), new RoleName('Admin'), 'Admin role', true, []);
    $repo = new FakeUserPermissionRepository;
    $repo->userRolesMap = ['user-1' => [$role], 'user-2' => []];

    $handler = new GetRolesForUsersHandler($repo);

    $result = $handler->handle(new GetRolesForUsersQuery(['user-1', 'user-2']));

    expect($result)->toHaveCount(2)
        ->and($result['user-1'])->toHaveCount(1)
        ->and($result['user-1'][0]->name->value)->toBe('Admin')
        ->and($result['user-2'])->toBeEmpty();
});

it('returns empty map for empty input', function (): void {
    $repo = new FakeUserPermissionRepository;
    $handler = new GetRolesForUsersHandler($repo);

    $result = $handler->handle(new GetRolesForUsersQuery([]));

    expect($result)->toBeEmpty();
});
