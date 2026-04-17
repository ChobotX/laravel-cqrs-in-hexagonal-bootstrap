<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Registry;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class CreateDefinitionVersionRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'schema' => ['required', 'array'],
            'schema.fields' => ['required', 'array'],
        ];
    }

    /** @return array<string, mixed> */
    public function schema(): array
    {
        /** @var array<string, mixed> $schema */
        $schema = $this->input('schema', []);

        return $schema;
    }
}
