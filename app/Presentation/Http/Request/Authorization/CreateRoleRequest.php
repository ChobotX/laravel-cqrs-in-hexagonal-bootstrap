<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Authorization;

use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Command\CreateRole\CreateRoleCommand;
use App\Presentation\Http\Request\FormRequest;
use Illuminate\Support\Str;

final class CreateRoleRequest extends FormRequest
{
    public function __construct(
        private readonly OrganizationContext $organizationContext,
    ) {
        parent::__construct();
    }

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

    public function toCommand(): CreateRoleCommand
    {
        /** @var list<array{permission: string, scope: string}> $permissions */
        $permissions = $this->validated('permissions');

        return new CreateRoleCommand(
            id: Str::uuid()->toString(),
            organizationId: $this->organizationContext->currentOrganizationId(),
            name: $this->string('name')->toString(),
            description: $this->string('description')->toString(),
            permissions: $permissions,
        );
    }
}
