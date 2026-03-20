<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

final readonly class RolePermissionMapper
{
    /** @param array{permission: string, scope: string} $data */
    public function map(array $data): RolePermission
    {
        $parts = explode('.', $data['permission']);
        $module = new Module($parts[0]);
        $feature = isset($parts[1]) ? new Feature($parts[1]) : null;
        $action = isset($parts[2]) ? Action::from($parts[2]) : null;

        return new RolePermission(
            new PermissionKey($module, $feature, $action),
            AccessScope::from($data['scope']),
        );
    }
}
