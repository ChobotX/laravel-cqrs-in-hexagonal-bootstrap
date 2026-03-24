<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Authorization;

use App\Domain\Authorization\Command\CreateRole\CreateRoleCommand;
use App\Presentation\Http\Request\FormRequest;
use Illuminate\Support\Str;

final class CreateRoleRequest extends FormRequest
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

    public function toCommand(): CreateRoleCommand
    {
        return new CreateRoleCommand(
            id: Str::uuid()->toString(),
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
