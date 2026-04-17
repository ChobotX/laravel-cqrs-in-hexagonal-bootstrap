<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Authorization;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class AssignRoleRequest extends FormRequest
{
    use HandlesFormRequest;

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
