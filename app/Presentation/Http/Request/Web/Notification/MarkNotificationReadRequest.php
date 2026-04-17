<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Notification;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class MarkNotificationReadRequest extends FormRequest
{
    use HandlesFormRequest;

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
