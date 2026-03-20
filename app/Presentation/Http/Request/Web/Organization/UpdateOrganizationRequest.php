<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Organization;

use App\Domain\Organization\Command\UpdateOrganization\UpdateOrganizationCommand;
use App\Domain\Organization\OrganizationSlug;
use App\Presentation\Http\Request\FormRequest;

final class UpdateOrganizationRequest extends FormRequest
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

    public function toCommand(): UpdateOrganizationCommand
    {
        return new UpdateOrganizationCommand(
            id: $this->routeString('organizationId'),
            name: $this->string('name')->toString(),
            slug: $this->string('slug')->toString(),
            description: $this->string('description')->toString(),
        );
    }
}
