<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidTwoFactorCodeException extends RuntimeException implements DomainException
{
    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.auth.invalid_two_factor_code');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
