<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidFlagValueException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $key,
        public readonly string $value,
        public readonly string $reason,
    ) {
        parent::__construct(sprintf('Invalid value [%s] for feature flag [%s]: %s', $value, $key, $reason));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_flag_value', [
            'key' => $this->key,
            'value' => $this->value,
            'reason' => $this->reason,
        ]);
    }

    public function statusCode(): int
    {
        return HttpStatus::UNPROCESSABLE_ENTITY;
    }
}
