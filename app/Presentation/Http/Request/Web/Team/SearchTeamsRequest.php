<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Team;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class SearchTeamsRequest extends FormRequest
{
    use HandlesFormRequest;

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
    public function excludeTeamIds(): array
    {
        /** @var list<string> $exclude */
        $exclude = $this->input('exclude', []);

        return $exclude;
    }
}
