<?php

declare(strict_types=1);

namespace App\Domain\Registry\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class EntryValidationException extends RuntimeException implements DomainException
{
    /** @param list<string> $errors */
    public function __construct(public readonly array $errors)
    {
        parent::__construct('Entry data validation failed.');
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.entry_validation_failed');
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
