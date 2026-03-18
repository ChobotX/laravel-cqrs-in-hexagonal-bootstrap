<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidRoleNameException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $invalidValue)
    {
        parent::__construct('Role name must not be empty.');
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_role_name');
    }

    public function statusCode(): int
    {
        return 422;
    }
}
