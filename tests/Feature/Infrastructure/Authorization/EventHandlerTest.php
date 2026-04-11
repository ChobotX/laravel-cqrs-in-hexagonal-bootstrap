<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Contract\Tenancy\TenantContext;
use App\Domain\Authorization\Constant\RoleFields;
use App\Domain\Authorization\Contract\Event\PermissionOverrideRemoved;
use App\Domain\Authorization\Contract\Event\PermissionOverrideSet;
use App\Domain\Authorization\Contract\Event\RoleAssignedToUser;
use App\Domain\Authorization\Contract\Event\RoleDeleted;
use App\Domain\Authorization\Contract\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Contract\Event\RoleUpdated;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnOverrideRemoved;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnOverrideSet;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleAssigned;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleDeleted;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleRevoked;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleUpdated;
use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Str;

function tenantVersionKey(string $userId): string
{
    $slug = app(TenantContext::class)->currentTenantSlug();

    return sprintf('%s:auth:version:%s', $slug, $userId);
}

function versionFor(CacheContract $cacheContract, string $userId): int
{
    /** @var int $version */
    $version = $cacheContract->get(tenantVersionKey($userId), 0);

    return $version;
}

it('increments auth version on role assigned', function (): void {
    $repository = app(CacheContract::class);

    app(RefreshAuthorizationOnRoleAssigned::class)
        ->handle(new RoleAssignedToUser('user-1', 'role-1', new DateTimeImmutable));

    expect(versionFor($repository, 'user-1'))->toBe(1);
});

it('increments auth version on role revoked', function (): void {
    $repository = app(CacheContract::class);

    app(RefreshAuthorizationOnRoleRevoked::class)
        ->handle(new RoleRevokedFromUser('user-1', 'role-1', new DateTimeImmutable));

    expect(versionFor($repository, 'user-1'))->toBe(1);
});

it('increments auth version on override set', function (): void {
    $repository = app(CacheContract::class);

    app(RefreshAuthorizationOnOverrideSet::class)
        ->handle(new PermissionOverrideSet('user-1', 'users.list.read', 'deny', new DateTimeImmutable));

    expect(versionFor($repository, 'user-1'))->toBe(1);
});

it('increments auth version on override removed', function (): void {
    $repository = app(CacheContract::class);

    app(RefreshAuthorizationOnOverrideRemoved::class)
        ->handle(new PermissionOverrideRemoved('user-1', 'users.list.read', new DateTimeImmutable));

    expect(versionFor($repository, 'user-1'))->toBe(1);
});

it('increments auth version for all users on role updated', function (): void {
    $repository = app(CacheContract::class);
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440500', 'name' => 'U1', 'email' => 'u1@evt.com']);
    $role = RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440501', 'name' => 'R', 'description' => 'R', 'is_system' => false]);
    UserRoleModel::create(['id' => Str::uuid()->toString(), 'user_id' => $user->id, 'role_id' => $role->id]);

    app(RefreshAuthorizationOnRoleUpdated::class)
        ->handle(new RoleUpdated($role->id, [new PropertyChange(RoleFields::NAME, 'R', 'R2')], new DateTimeImmutable));

    expect(versionFor($repository, $user->id))->toBe(1);
});

it('increments auth version for all users on role deleted', function (): void {
    $repository = app(CacheContract::class);
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440502', 'name' => 'U2', 'email' => 'u2@evt.com']);
    $role = RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440503', 'name' => 'R2', 'description' => 'R2', 'is_system' => false]);
    UserRoleModel::create(['id' => Str::uuid()->toString(), 'user_id' => $user->id, 'role_id' => $role->id]);

    app(RefreshAuthorizationOnRoleDeleted::class)
        ->handle(new RoleDeleted($role->id, new DateTimeImmutable));

    expect(versionFor($repository, $user->id))->toBe(1);
});
