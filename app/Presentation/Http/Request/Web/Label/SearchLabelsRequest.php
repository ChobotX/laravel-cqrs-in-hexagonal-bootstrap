<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Label;

use App\Presentation\Http\Request\FormRequest;

final class SearchLabelsRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:100'],
            'namespace' => ['required', 'string'],
            'exclude' => ['sometimes', 'array'],
            'exclude.*' => ['uuid'],
        ];
    }

    public function searchTerm(): string
    {
        return $this->string('q')->toString();
    }

    public function namespace(): string
    {
        return $this->string('namespace')->toString();
    }

    /** @return list<string> */
    public function excludeLabelIds(): array
    {
        /** @var list<string> $exclude */
        $exclude = $this->input('exclude', []);

        return $exclude;
    }
}
