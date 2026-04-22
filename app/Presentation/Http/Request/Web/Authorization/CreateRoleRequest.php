<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Authorization;

use App\Domain\Authorization\Contract\Command\CreateRoleCommand;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class CreateRoleRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*.enabled' => ['sometimes', 'string'],
            'permissions.*.scope' => ['sometimes', 'in:all,team_tree,team,own'],
        ];
    }

    public function toCommand(string $id): CreateRoleCommand
    {
        return new CreateRoleCommand(
            id: $id,
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
