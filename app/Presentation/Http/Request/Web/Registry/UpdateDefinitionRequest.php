<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Registry;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateDefinitionRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function name(): string
    {
        return $this->string('name')->toString();
    }
}
