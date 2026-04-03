<?php

declare(strict_types=1);

namespace App\Domain\Registry\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class DefinitionVersionNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $versionId)
    {
        parent::__construct(sprintf('Definition version [%s] not found.', $versionId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.definition_version_not_found');
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}
