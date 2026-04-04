<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Service;

use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\ValueObject\RolePermission;
use App\Domain\Authorization\Enum\Action;
use App\Domain\Authorization\Enum\Feature;
use App\Domain\Authorization\ValueObject\Module;
use App\Domain\Authorization\ValueObject\PermissionKey;

final readonly class RolePermissionMapper
{
    /** @param array{permission: string, scope: string} $data */
    public function map(array $data): RolePermission
    {
        $parts = explode('.', $data['permission']);

        return new RolePermission(
            new PermissionKey(
                new Module($parts[PermissionKey::MODULE_INDEX]),
                isset($parts[PermissionKey::FEATURE_INDEX]) ? new Feature($parts[PermissionKey::FEATURE_INDEX]) : null,
                isset($parts[PermissionKey::ACTION_INDEX]) ? Action::from($parts[PermissionKey::ACTION_INDEX]) : null,
            ),
            AccessScope::from($data['scope']),
        );
    }
}
