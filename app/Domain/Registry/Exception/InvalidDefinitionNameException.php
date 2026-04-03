<?php

declare(strict_types=1);

namespace App\Domain\Registry\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidDefinitionNameException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $invalidValue)
    {
        parent::__construct(sprintf('Value [%s] is not a valid definition name.', $invalidValue));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_definition_name', ['value' => $this->invalidValue]);
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
