<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class ImpersonationNotAllowedException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $userId = '')
    {
        parent::__construct('Impersonation is not allowed.');
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.impersonation_not_allowed');
    }

    public function statusCode(): int
    {
        return HttpStatus::FORBIDDEN;
    }
}
