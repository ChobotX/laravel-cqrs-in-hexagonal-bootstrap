<?php

declare(strict_types=1);

namespace App\Domain\Label\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidLabelNameException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $invalidValue)
    {
        parent::__construct(sprintf('Value [%s] is not a valid label name.', $invalidValue));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_label_name', ['value' => $this->invalidValue]);
    }

    public function statusCode(): int
    {
        return 422;
    }
}
