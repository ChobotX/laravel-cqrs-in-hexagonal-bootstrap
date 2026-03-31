<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Notification;

use App\Presentation\Http\Request\FormRequest;

final class MarkNotificationReadRequest extends FormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [];
    }

    public function notificationId(): string
    {
        return $this->routeString('notificationId');
    }
}
