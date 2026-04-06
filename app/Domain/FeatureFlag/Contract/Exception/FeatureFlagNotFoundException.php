<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class FeatureFlagNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $key)
    {
        parent::__construct(sprintf('Feature flag with key [%s] not found.', $key));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.feature_flag_not_found', ['key' => $this->key]);
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}
