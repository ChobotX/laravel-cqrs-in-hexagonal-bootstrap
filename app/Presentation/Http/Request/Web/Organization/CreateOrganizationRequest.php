<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Organization;

use App\Domain\Organization\Command\CreateOrganization\CreateOrganizationCommand;
use App\Domain\Organization\OrganizationSlug;
use App\Presentation\Http\Request\FormRequest;
use Illuminate\Support\Str;

final class CreateOrganizationRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:'.OrganizationSlug::MAX_LENGTH, 'regex:'.OrganizationSlug::SLUG_PATTERN, 'min:'.OrganizationSlug::MIN_LENGTH],
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
