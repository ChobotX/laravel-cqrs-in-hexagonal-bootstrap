<?php

declare(strict_types=1);

namespace App\Infrastructure\Authorization;

use App\Application\Authorization\ShareableResourceRegistry;
use App\Domain\Authorization\Contract\Enum\Action;
use App\Domain\Authorization\Contract\Repository\RecordShareRepository;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Contract\Service\AccessDecision;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\Authorization\Contract\ValueObject\EffectivePermission;
use App\Domain\Authorization\Service\PermissionResolver;

final readonly class ResolverAuthorizationChecker implements AuthorizationChecker
{
    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     */
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
        private RecordShareRepository $recordShareRepository,
        private PermissionResolver $permissionResolver,
        private ShareableResourceRegistry $shareableResourceRegistry,
        private array $availableModules,
    ) {}

    public function can(string $userId, string $permission): bool
    {
        $effectivePermissions = $this->resolveEffectivePermissions($userId);

        return array_any($effectivePermissions, fn ($effectivePermission): bool => (string) $effectivePermission->permissionKey === $permission && $effectivePermission->granted);
    }

    public function canWithScope(string $userId, string $permission): AccessDecision
    {
        $effectivePermissions = $this->resolveEffectivePermissions($userId);

        foreach ($effectivePermissions as $effectivePermission) {
            if ((string) $effectivePermission->permissionKey === $permission) {
                return new SimpleAccessDecision(
                    granted: $effectivePermission->granted,
                    scope: $effectivePermission->scope->value,
                );
            }
        }

        return new SimpleAccessDecision(granted: false, scope: 'all');
    }

    /** @return list<string> */
    public function accessibleResourceIds(
        string $userId,
        string $resourceType,
        string $action,
    ): array {
        return $this->recordShareRepository->accessibleResourceIds(
            $userId,
            $resourceType,
            Action::from($action),
        );
    }

    public function supportsResourceSharing(string $resourceType): bool
    {
        return $this->shareableResourceRegistry->supports($resourceType);
    }

    public function canShareResource(string $userId, string $resourceType): bool
    {
        return $this->can($userId, $this->shareableResourceRegistry->updatePermission($resourceType));
    }

    public function canViewResourceShares(string $userId, string $resourceType): bool
    {
        return $this->can($userId, $this->shareableResourceRegistry->readPermission($resourceType));
    }

    /**
     * @return list<EffectivePermission>
     */
    private function resolveEffectivePermissions(string $userId): array
    {
        $roles = $this->userPermissionRepository->userRoles($userId);
        $overrides = $this->userPermissionRepository->userOverrides($userId);

        return $this->permissionResolver->resolve($roles, $overrides, $this->availableModules);
    }
}
