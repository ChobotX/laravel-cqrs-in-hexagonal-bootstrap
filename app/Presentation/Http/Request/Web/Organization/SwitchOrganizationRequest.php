<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Organization;

use App\Presentation\Http\Request\FormRequest;

final class SwitchOrganizationRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'uuid'],
        ];
    }

    public function organizationId(): string
    {
        return $this->string('organization_id')->toString();
    }
}
