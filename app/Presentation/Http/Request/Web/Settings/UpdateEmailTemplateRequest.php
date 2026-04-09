<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Presentation\Http\Request\FormRequest;

final class UpdateEmailTemplateRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'subject_template' => ['required', 'string', 'max:500'],
            'body_template' => ['required', 'string'],
        ];
    }
}
