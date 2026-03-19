<?php

declare(strict_types=1);

namespace App\Domain\Organization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class OrganizationNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $id)
    {
        parent::__construct(sprintf('Organization with id [%s] not found.', $id));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.organization_not_found', ['id' => $this->id]);
    }

    public function statusCode(): int
    {
        return 404;
    }
}
