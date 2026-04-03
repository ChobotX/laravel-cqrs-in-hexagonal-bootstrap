<?php

declare(strict_types=1);

namespace App\Domain\Registry\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class EntryNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $entryId)
    {
        parent::__construct(sprintf('Entry [%s] not found.', $entryId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.entry_not_found');
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}
