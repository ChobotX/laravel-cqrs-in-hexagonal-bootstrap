<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Authorization;

use App\Domain\Authorization\Contract\Command\UpdateRoleCommand;
use App\Presentation\Http\Request\FormRequest;

final class UpdateRoleRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*.enabled' => ['sometimes', 'string'],
            'permissions.*.scope' => ['sometimes', 'string'],
        ];
    }

    public function toCommand(): UpdateRoleCommand
    {
        return new UpdateRoleCommand(
            id: $this->routeString('roleId'),
            name: $this->string('name')->toString(),
            description: $this->string('description')->toString(),
            permissions: $this->buildPermissions(),
        );
    }

    /** @return list<array{permission: string, scope: string}> */
    private function buildPermissions(): array
    {
        $permissions = [];

        /** @var array<string, array{enabled?: string, scope?: string}> $raw */
        $raw = $this->input('permissions', []);

        foreach ($raw as $key => $data) {
            if (isset($data['enabled'])) {
                $permissions[] = [
                    'permission' => $key,
                    'scope' => $data['scope'] ?? 'all',
                ];
            }
        }

        return $permissions;
    }
}
