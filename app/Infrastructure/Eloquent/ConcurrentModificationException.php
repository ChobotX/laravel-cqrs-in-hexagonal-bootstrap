<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class ConcurrentModificationException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $modelClass,
        public readonly string $modelId,
    ) {
        parent::__construct(sprintf('Concurrent modification detected on %s with ID %s', $modelClass, $modelId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.concurrent_modification');
    }

    public function statusCode(): int
    {
        return HttpStatus::CONFLICT;
    }
}
