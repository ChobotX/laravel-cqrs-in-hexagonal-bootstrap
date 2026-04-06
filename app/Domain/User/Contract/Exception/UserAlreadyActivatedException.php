<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class UserAlreadyActivatedException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $id)
    {
        parent::__construct(sprintf('User with id [%s] has already been activated.', $id));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.user_already_activated');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
