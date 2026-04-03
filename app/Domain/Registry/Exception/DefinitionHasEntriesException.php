<?php

declare(strict_types=1);

namespace App\Domain\Registry\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class DefinitionHasEntriesException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $definitionId)
    {
        parent::__construct(sprintf('Definition [%s] has entries and cannot be deleted.', $definitionId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.definition_has_entries');
    }

    public function statusCode(): int
    {
        return HttpStatus::CONFLICT;
    }
}
