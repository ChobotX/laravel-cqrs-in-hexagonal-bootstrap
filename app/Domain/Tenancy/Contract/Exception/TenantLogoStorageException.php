<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class TenantLogoStorageException extends RuntimeException implements DomainException
{
    public function __construct(string $tenantId)
    {
        parent::__construct(sprintf('Failed to store logo for tenant [%s].', $tenantId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.tenant_logo_storage_failed');
    }

    public function statusCode(): int
    {
        return HttpStatus::INTERNAL_SERVER_ERROR;
    }
}
