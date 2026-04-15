<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Authorization;

use App\Domain\Authorization\Contract\Command\CreateRoleCommand;
use App\Presentation\Http\Request\FormRequest;
use Illuminate\Support\Str;

final class CreateRoleRequest extends FormRequest
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
            'permissions.*.scope' => ['required', 'in:all,team_tree,team,own'],
        ];
    }

    public function toCommand(): CreateRoleCommand
    {
        /** @var list<array{permission: string, scope: string}> $permissions */
        $permissions = $this->validated('permissions');

        return new CreateRoleCommand(
            id: Str::uuid()->toString(),
            name: $this->string('name')->toString(),
            description: $this->string('description')->toString(),
            permissions: $permissions,
        );
    }
}
