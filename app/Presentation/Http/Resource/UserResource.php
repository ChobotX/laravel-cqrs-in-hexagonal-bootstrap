<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resource;

use App\Domain\User\Contract\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/** @property User $resource */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, string>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id->value,
            'name' => $this->resource->name->value,
            'email' => $this->resource->email->value,
        ];
    }
}
