<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class PasswordPreviouslyUsedException extends RuntimeException implements DomainException
{
    public function __construct()
    {
        parent::__construct('New password matches a recently used password.');
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.password_previously_used');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
