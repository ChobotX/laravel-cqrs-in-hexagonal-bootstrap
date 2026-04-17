<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\AuditLog;

use App\Presentation\Http\Request\HandlesFormRequest;
use DateTimeImmutable;
use Illuminate\Foundation\Http\FormRequest;

final class ListAuditLogRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'entity_type' => ['sometimes', 'nullable', 'string', 'max:100'],
            'entity_id' => ['sometimes', 'nullable', 'uuid'],
            'user_id' => ['sometimes', 'nullable', 'uuid'],
            'trace_id' => ['sometimes', 'nullable', 'string', 'max:64'],
            'from' => ['sometimes', 'nullable', 'date'],
            'to' => ['sometimes', 'nullable', 'date'],
        ];
    }

    public function entityType(): ?string
    {
        $value = $this->input('entity_type');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function entityId(): ?string
    {
        $value = $this->input('entity_id');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function userId(): ?string
    {
        $value = $this->input('user_id');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function traceId(): ?string
    {
        $value = $this->input('trace_id');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public function from(): ?DateTimeImmutable
    {
        $value = $this->input('from');

        return is_string($value) && $value !== '' ? new DateTimeImmutable($value) : null;
    }

    public function to(): ?DateTimeImmutable
    {
        $value = $this->input('to');

        return is_string($value) && $value !== '' ? new DateTimeImmutable($value) : null;
    }
}
