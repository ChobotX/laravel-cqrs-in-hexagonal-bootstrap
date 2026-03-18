<?php

declare(strict_types=1);

use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\OverrideType;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Query\GetUserOverrides\GetUserOverridesHandler;
use App\Domain\Authorization\Query\GetUserOverrides\GetUserOverridesQuery;
use App\Domain\Authorization\UserPermissionOverride;
use Tests\Helper\FakeUserPermissionRepository;

it('returns overrides for a user in an organization', function (): void {
    $override = new UserPermissionOverride(
        new PermissionKey(new Module('users'), new Feature('list'), Action::Read),
        OverrideType::Grant,
        AccessScope::All,
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userOverridesMap['00000000-0000-0000-0000-000000000010:00000000-0000-0000-0000-000000000001'] = [$override];

    $handler = new GetUserOverridesHandler($userPermRepo);

    $result = $handler->handle(new GetUserOverridesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($result)->toHaveCount(1)
        ->and((string) $result[0]->permissionKey)->toBe('users.list.read');
});

it('returns empty list when user has no overrides', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;

    $handler = new GetUserOverridesHandler($userPermRepo);

    $result = $handler->handle(new GetUserOverridesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($result)->toHaveCount(0);
});
