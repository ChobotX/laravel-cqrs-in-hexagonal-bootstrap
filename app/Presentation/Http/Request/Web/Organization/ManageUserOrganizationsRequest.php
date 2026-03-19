<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Organization;

use App\Presentation\Http\Request\FormRequest;

final class ManageUserOrganizationsRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            '_action' => ['required', 'in:add_organization,remove_organization'],
            'organization_id' => ['required', 'uuid'],
        ];
    }

    public function action(): string
    {
        return $this->string('_action')->toString();
    }

    public function organizationId(): string
    {
        return $this->string('organization_id')->toString();
    }
}
