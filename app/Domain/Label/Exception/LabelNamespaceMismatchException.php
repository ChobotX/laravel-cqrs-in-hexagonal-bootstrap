<?php

declare(strict_types=1);

namespace App\Domain\Label\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class LabelNamespaceMismatchException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $labelId,
        public readonly string $expectedNamespace,
        public readonly string $actualNamespace,
    ) {
        parent::__construct(sprintf(
            'Label [%s] belongs to namespace [%s], expected [%s].',
            $labelId,
            $actualNamespace,
            $expectedNamespace,
        ));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.label_namespace_mismatch');
    }

    public function statusCode(): int
    {
        return 400;
    }
}
