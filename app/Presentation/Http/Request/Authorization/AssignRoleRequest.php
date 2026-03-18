<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Authorization;

use App\Presentation\Http\Request\FormRequest;

final class AssignRoleRequest extends FormRequest
{
    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'role_id' => ['required', 'uuid'],
        ];
    }
}
