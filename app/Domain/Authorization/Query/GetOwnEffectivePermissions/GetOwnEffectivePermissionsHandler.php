<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetOwnEffectivePermissions;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\EffectivePermission;
use App\Domain\Authorization\PermissionResolver;
use App\Domain\Authorization\UserPermissionRepository;

/** @implements QueryHandler<GetOwnEffectivePermissionsQuery, list<EffectivePermission>> */
final readonly class GetOwnEffectivePermissionsHandler implements QueryHandler
{
    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     */
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
        private PermissionResolver $permissionResolver,
        private array $availableModules,
    ) {}

    /** @return list<EffectivePermission> */
    public function handle(Query $query): array
    {
        $roles = $this->userPermissionRepository->userRoles($query->userId);
        $overrides = $this->userPermissionRepository->userOverrides($query->userId);

        return $this->permissionResolver->resolve($roles, $overrides, $this->availableModules);
    }
}
