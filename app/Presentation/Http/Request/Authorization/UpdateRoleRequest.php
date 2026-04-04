<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Authorization;

use App\Domain\Authorization\Contract\Command\UpdateRoleCommand;
use App\Presentation\Http\Request\FormRequest;

final class UpdateRoleRequest extends FormRequest
{
    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*.permission' => ['required', 'string'],
            'permissions.*.scope' => ['required', 'in:all,team,own,shared'],
        ];
    }

    public function toCommand(): UpdateRoleCommand
    {
        /** @var list<array{permission: string, scope: string}> $permissions */
        $permissions = $this->validated('permissions');

        return new UpdateRoleCommand(
            id: $this->routeString('roleId'),
            name: $this->string('name')->toString(),
            description: $this->string('description')->toString(),
            permissions: $permissions,
        );
    }
}
