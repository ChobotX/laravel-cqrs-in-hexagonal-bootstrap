<?php

declare(strict_types=1);

namespace App\Domain\Organization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidOrganizationNameException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $invalidValue)
    {
        parent::__construct(sprintf('Organization name must not be empty, got [%s].', $invalidValue));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_organization_name');
    }

    public function statusCode(): int
    {
        return 422;
    }
}
