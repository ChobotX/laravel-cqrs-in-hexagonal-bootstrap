<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class InvalidFlagKeyException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $key)
    {
        parent::__construct(sprintf('Invalid feature flag key format: [%s]. Expected lowercase dot-notation (e.g. group.flag-name).', $key));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.invalid_flag_key', ['key' => $this->key]);
    }

    public function statusCode(): int
    {
        return HttpStatus::BAD_REQUEST;
    }
}
