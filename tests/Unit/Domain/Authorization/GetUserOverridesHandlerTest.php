<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\Query\GetUserOverridesQuery;
use App\Domain\Authorization\Contract\ValueObject\UserPermissionOverride;
use App\Domain\Authorization\Enum\Action;
use App\Domain\Authorization\Enum\OverrideType;
use App\Domain\Authorization\Handler\Query\GetUserOverridesHandler;
use App\Domain\Authorization\ValueObject\Feature;
use App\Domain\Authorization\ValueObject\Module;
use App\Domain\Authorization\ValueObject\PermissionKey;
use Tests\Helper\FakeUserPermissionRepository;

it('returns overrides for a user', function (): void {
    $override = new UserPermissionOverride(
        new PermissionKey(new Module('users'), new Feature('list'), Action::Read),
        OverrideType::Grant,
        AccessScope::All,
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userOverridesMap['00000000-0000-0000-0000-000000000010'] = [$override];

    $handler = new GetUserOverridesHandler($userPermRepo);

    $result = $handler->handle(new GetUserOverridesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(1)
        ->and((string) $result[0]->permissionKey)->toBe('users.list.read');
});

it('returns empty list when user has no overrides', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;

    $handler = new GetUserOverridesHandler($userPermRepo);

    $result = $handler->handle(new GetUserOverridesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(0);
});
