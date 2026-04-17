<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class PreviewEmailTemplateRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'templateType' => ['required', 'string'],
            'locale' => ['required', 'string'],
            'subjectTemplate' => ['required', 'string', 'max:500'],
            'bodyTemplate' => ['required', 'string'],
        ];
    }
}
