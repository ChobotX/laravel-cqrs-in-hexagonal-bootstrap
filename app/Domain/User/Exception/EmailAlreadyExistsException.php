<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class EmailAlreadyExistsException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $email)
    {
        parent::__construct(sprintf('A user with email [%s] already exists.', $email));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.email_already_exists', ['email' => $this->email]);
    }

    public function statusCode(): int
    {
        return 409;
    }
}
