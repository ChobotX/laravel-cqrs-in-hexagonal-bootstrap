<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resource;

use App\Domain\Authorization\Role;
use App\Domain\Authorization\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/** @property Role $resource */
final class RoleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id->value,
            'name' => $this->resource->name->value,
            'description' => $this->resource->description,
            'is_system' => $this->resource->isSystem,
            'permissions' => array_map(
                static fn (RolePermission $rolePermission): array => [
                    'permission' => (string) $rolePermission->permissionKey,
                    'scope' => $rolePermission->scope->value,
                ],
                $this->resource->permissions,
            ),
        ];
    }
}
