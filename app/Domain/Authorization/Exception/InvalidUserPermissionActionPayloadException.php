<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidUserPermissionActionPayloadException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $action, public readonly string $missingField)
    {
        parent::__construct(sprintf('Action [%s] requires field [%s].', $action, $missingField));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_user_permission_action_payload', [
            'action' => $this->action,
            'field' => $this->missingField,
        ]);
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
