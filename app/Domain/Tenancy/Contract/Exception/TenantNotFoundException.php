<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

/**
 * Thrown when no tenant matches the supplied slug, domain, or other resolution input.
 */
final class TenantNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(
        /** Value used during resolution (slug, host, or id depending on caller). */
        public readonly string $identifier,
    ) {
        parent::__construct(sprintf('Tenant not found for identifier [%s].', $identifier));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.tenant_not_found', ['identifier' => $this->identifier]);
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}
