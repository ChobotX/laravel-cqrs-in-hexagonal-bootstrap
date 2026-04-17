<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\GridPreset;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class SaveGridPresetRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'grid_name' => ['required', 'string', 'max:100'],
            'filters' => ['required', 'string'],
            'sorting' => ['required', 'string'],
            'search' => ['nullable', 'string', 'max:200'],
            'is_default' => ['sometimes', 'boolean'],
            'preset_id' => ['nullable', 'string', 'uuid'],
            'scope' => ['sometimes', 'string', 'in:personal,team,global'],
            'team_id' => ['required_if:scope,team', 'nullable', 'string', 'uuid'],
        ];
    }
}
