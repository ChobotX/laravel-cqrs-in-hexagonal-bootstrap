<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\User;

use App\Presentation\Http\Request\FormRequest;

final class SearchUsersRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:100'],
            'exclude' => ['sometimes', 'array'],
            'exclude.*' => ['uuid'],
        ];
    }

    public function searchTerm(): string
    {
        return $this->string('q')->toString();
    }

    /** @return list<string> */
    public function excludeUserIds(): array
    {
        /** @var list<string> $exclude */
        $exclude = $this->input('exclude', []);

        return $exclude;
    }
}
