<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InactiveTenantException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $identifier)
    {
        parent::__construct(sprintf('Tenant [%s] is inactive.', $identifier));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.tenant_inactive', ['identifier' => $this->identifier]);
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}
