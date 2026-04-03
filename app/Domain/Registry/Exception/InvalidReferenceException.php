<?php

declare(strict_types=1);

namespace App\Domain\Registry\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidReferenceException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $fieldName,
        public readonly string $entryId,
    ) {
        parent::__construct(sprintf('Field [%s] references invalid entry [%s].', $fieldName, $entryId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_reference', [
            'field' => $this->fieldName,
            'entry' => $this->entryId,
        ]);
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
