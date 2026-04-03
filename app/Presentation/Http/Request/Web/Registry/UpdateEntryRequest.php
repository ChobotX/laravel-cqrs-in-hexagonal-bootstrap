<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Registry;

use App\Presentation\Http\Request\FormRequest;

final class UpdateEntryRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'data' => ['required', 'array'],
        ];
    }

    public function title(): string
    {
        return $this->string('title')->toString();
    }

    /** @return array<string, mixed> */
    public function entryData(): array
    {
        /** @var array<string, mixed> $data */
        $data = $this->validated()['data'];

        return $data;
    }
}
