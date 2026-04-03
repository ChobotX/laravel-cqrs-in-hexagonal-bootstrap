<?php

declare(strict_types=1);

namespace App\Domain\Registry\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class DefinitionNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $definitionId)
    {
        parent::__construct(sprintf('Definition [%s] not found.', $definitionId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.definition_not_found');
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}
