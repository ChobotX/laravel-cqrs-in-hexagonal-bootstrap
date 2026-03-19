<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Organization;

use App\Domain\Organization\Command\CreateOrganization\CreateOrganizationCommand;
use App\Presentation\Http\Request\FormRequest;
use Illuminate\Support\Str;

final class CreateOrganizationRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', 'min:2'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function toCommand(): CreateOrganizationCommand
    {
        return new CreateOrganizationCommand(
            id: Str::uuid()->toString(),
            name: $this->string('name')->toString(),
            slug: $this->string('slug')->toString(),
            description: $this->string('description')->toString(),
        );
    }
}
