<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

/**
 * Thrown when registering or updating a user would reuse an email that is already taken.
 */
final class EmailAlreadyExistsException extends RuntimeException implements DomainException
{
    public function __construct(
        /** Email address that conflicts with an existing user. */
        public readonly string $email,
    ) {
        parent::__construct(sprintf('A user with email [%s] already exists.', $email));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.email_already_exists', ['email' => $this->email]);
    }

    public function statusCode(): int
    {
        return HttpStatus::CONFLICT;
    }
}
