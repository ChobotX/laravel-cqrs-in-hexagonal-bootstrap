<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

/**
 * Thrown when submitted user input fails domain validation before persistence (e.g. empty display name).
 */
final class InvalidUserDataException extends RuntimeException implements DomainException
{
    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_user_data_empty_name');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
