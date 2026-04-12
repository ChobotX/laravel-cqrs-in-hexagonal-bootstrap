<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidTenantDisplayTimezoneException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $value,
    ) {
        parent::__construct(sprintf('Invalid IANA display timezone: %s.', $value));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_tenant_display_timezone');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
