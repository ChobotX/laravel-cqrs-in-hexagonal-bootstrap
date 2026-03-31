<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class RoleAlreadyExistsException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $name)
    {
        parent::__construct(sprintf('A role with name [%s] already exists.', $name));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.role_already_exists', ['name' => $this->name]);
    }

    public function statusCode(): int
    {
        return HttpStatus::CONFLICT;
    }
}
