<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Label;

use App\Presentation\Http\Request\FormRequest;

final class CreateLabelRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'namespace' => ['required', 'string'],
            'name' => ['required', 'string', 'max:100'],
        ];
    }

    public function namespace(): string
    {
        return $this->string('namespace')->toString();
    }

    public function name(): string
    {
        return $this->string('name')->toString();
    }
}
