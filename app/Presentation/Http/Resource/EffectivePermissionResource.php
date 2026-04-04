<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resource;

use App\Domain\Authorization\Contract\EffectivePermission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/** @property EffectivePermission $resource */
final class EffectivePermissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'permission' => (string) $this->resource->permissionKey,
            'granted' => $this->resource->granted,
            'scope' => $this->resource->scope->value,
            'source' => $this->resource->source,
        ];
    }
}
