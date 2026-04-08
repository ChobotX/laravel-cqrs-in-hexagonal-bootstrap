<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\GridPreset;

use App\Presentation\Http\Request\FormRequest;

final class ListGridPresetsRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'grid_name' => ['required', 'string', 'max:100'],
        ];
    }
}
