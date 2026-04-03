<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Registry;

use App\Presentation\Http\Request\FormRequest;

final class CreateDefinitionRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'namespace' => ['required', 'string', 'max:63', 'regex:/^[a-z][a-z0-9_]*$/'],
            'slug' => ['required', 'string', 'max:63', 'regex:/^[a-z][a-z0-9_]*$/'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function namespace(): string
    {
        return $this->string('namespace')->toString();
    }

    public function slug(): string
    {
        return $this->string('slug')->toString();
    }

    public function name(): string
    {
        return $this->string('name')->toString();
    }
}
