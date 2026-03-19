<?php

declare(strict_types=1);

namespace App\Domain\Organization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class OrganizationSlugAlreadyExistsException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $slug)
    {
        parent::__construct(sprintf('An organization with slug [%s] already exists.', $slug));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.organization_slug_already_exists', ['slug' => $this->slug]);
    }

    public function statusCode(): int
    {
        return 409;
    }
}
