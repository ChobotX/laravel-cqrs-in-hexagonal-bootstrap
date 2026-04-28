<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidMailTransportException extends RuntimeException implements DomainException
{
    public function __construct(private readonly string $messageKey)
    {
        parent::__construct($messageKey);
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate($this->messageKey);
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
