<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class TenantDisplayNameNotConfiguredException extends RuntimeException implements DomainException
{
    public function __construct()
    {
        parent::__construct('Tenant display_name is missing or empty in tenant_preferences.');
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.tenant_display_name_not_configured');
    }

    public function statusCode(): int
    {
        return HttpStatus::INTERNAL_SERVER_ERROR;
    }
}
