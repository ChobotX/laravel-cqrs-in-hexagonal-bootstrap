<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Application\Pagination\Pagination;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class ListEmailLogsRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:'.Pagination::MAX_PER_PAGE],
            'template_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'recipient_id' => ['sometimes', 'nullable', 'uuid'],
        ];
    }

    public function templateType(): ?string
    {
        $value = $this->input('template_type');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function recipientId(): ?string
    {
        $value = $this->input('recipient_id');

        return is_string($value) && $value !== '' ? $value : null;
    }
}
