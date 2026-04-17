<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Authorization;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class SetPermissionOverrideRequest extends FormRequest
{
    use HandlesFormRequest;

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'permission' => ['required', 'string'],
            'type' => ['required', 'in:grant,deny'],
            'scope' => ['required', 'in:all,team_tree,team,own'],
        ];
    }
}
