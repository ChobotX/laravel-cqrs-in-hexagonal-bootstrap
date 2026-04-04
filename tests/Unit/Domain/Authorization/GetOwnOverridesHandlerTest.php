<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Contract\Query\GetOwnOverrides\GetOwnOverridesQuery;
use App\Domain\Authorization\Contract\UserPermissionOverride;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\OverrideType;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Query\GetOwnOverrides\GetOwnOverridesHandler;
use Tests\Helper\FakeUserPermissionRepository;

it('returns own overrides', function (): void {
    $override = new UserPermissionOverride(
        new PermissionKey(new Module('users'), new Feature('list'), Action::Read),
        OverrideType::Grant,
        AccessScope::All,
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userOverridesMap['00000000-0000-0000-0000-000000000010'] = [$override];

    $handler = new GetOwnOverridesHandler($userPermRepo);

    $result = $handler->handle(new GetOwnOverridesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(1)
        ->and((string) $result[0]->permissionKey)->toBe('users.list.read');
});

it('returns empty list when user has no overrides', function (): void {
    $handler = new GetOwnOverridesHandler(new FakeUserPermissionRepository);

    $result = $handler->handle(new GetOwnOverridesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(0);
});
