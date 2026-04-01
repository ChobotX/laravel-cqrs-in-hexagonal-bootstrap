<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\PermissionResolver;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleName;
use App\Domain\Authorization\RolePermission;
use App\Infrastructure\Authorization\ResolverAuthorizationChecker;
use Tests\Helper\FakeRecordShareRepository;
use Tests\Helper\FakeUserPermissionRepository;

function resolverChecker(?FakeUserPermissionRepository $fakeUserPermissionRepository = null): ResolverAuthorizationChecker
{
    return new ResolverAuthorizationChecker(
        userPermissionRepository: $fakeUserPermissionRepository ?? new FakeUserPermissionRepository,
        recordShareRepository: new FakeRecordShareRepository,
        permissionResolver: new PermissionResolver,
        availableModules: [
            'users' => ['features' => ['list' => ['actions' => ['read', 'create', 'update', 'delete']]]],
        ],
    );
}

it('returns true when permission is granted', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440001'),
        new RoleName('Super Admin'),
        'SA',
        true,
        [],
    );
    $userPermRepo->userRolesMap['user-1'] = [$role];

    $resolverAuthorizationChecker = resolverChecker($userPermRepo);

    expect($resolverAuthorizationChecker->can('user-1', 'users.list.read'))->toBeTrue();
});

it('returns false when permission is not granted', function (): void {
    $resolverAuthorizationChecker = resolverChecker();

    expect($resolverAuthorizationChecker->can('user-1', 'users.list.read'))->toBeFalse();
});

it('returns access decision with scope', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440002'),
        new RoleName('Editor'),
        'Ed',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::Team)],
    );
    $userPermRepo->userRolesMap['user-1'] = [$role];

    $resolverAuthorizationChecker = resolverChecker($userPermRepo);
    $accessDecision = $resolverAuthorizationChecker->canWithScope('user-1', 'users.list.read');

    expect($accessDecision->granted())->toBeTrue();
    expect($accessDecision->scope())->toBe('team');
});

it('returns denied decision for non-existent permission', function (): void {
    $resolverAuthorizationChecker = resolverChecker();
    $accessDecision = $resolverAuthorizationChecker->canWithScope('user-1', 'users.list.read');

    expect($accessDecision->granted())->toBeFalse();
});

it('returns denied decision for unknown permission key', function (): void {
    $resolverAuthorizationChecker = resolverChecker();
    $accessDecision = $resolverAuthorizationChecker->canWithScope('user-1', 'unknown.permission');

    expect($accessDecision->granted())->toBeFalse();
    expect($accessDecision->scope())->toBe('all');
});

it('delegates accessibleResourceIds to record share repository', function (): void {
    $resolverAuthorizationChecker = resolverChecker();
    $result = $resolverAuthorizationChecker->accessibleResourceIds('user-1', 'document', 'read');

    expect($result)->toBe([]);
});
