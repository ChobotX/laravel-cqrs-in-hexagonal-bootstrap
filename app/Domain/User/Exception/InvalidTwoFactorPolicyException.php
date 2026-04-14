<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidTwoFactorPolicyException extends RuntimeException implements DomainException
{
    public function __construct(
        private readonly string $translationKey,
    ) {
        parent::__construct($translationKey);
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate($this->translationKey);
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
