<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidTenantNameException extends RuntimeException implements DomainException
{
    public function __construct()
    {
        parent::__construct('Tenant name must not be empty.');
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_tenant_name');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
