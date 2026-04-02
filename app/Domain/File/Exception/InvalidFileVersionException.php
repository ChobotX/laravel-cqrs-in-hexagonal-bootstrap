<?php

declare(strict_types=1);

namespace App\Domain\File\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidFileVersionException extends RuntimeException implements DomainException
{
    public function __construct(public readonly int $invalidValue)
    {
        parent::__construct(sprintf('Value [%d] is not a valid file version.', $invalidValue));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_file_version', ['value' => (string) $this->invalidValue]);
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
