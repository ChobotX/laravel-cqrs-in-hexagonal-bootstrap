<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Notification;

use App\Presentation\Http\Request\FormRequest;

final class ListNotificationsRequest extends FormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'filter' => ['sometimes', 'string', 'in:all,unread,read'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function filter(): string
    {
        return $this->string('filter', 'all')->toString();
    }

    public function page(): int
    {
        return $this->integer('page', 1);
    }

    public function perPage(): int
    {
        return $this->integer('per_page', 15);
    }
}
