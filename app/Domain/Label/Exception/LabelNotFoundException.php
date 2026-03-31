<?php

declare(strict_types=1);

namespace App\Domain\Label\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class LabelNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $labelId)
    {
        parent::__construct(sprintf('Label [%s] not found.', $labelId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.label_not_found');
    }

    public function statusCode(): int
    {
        return 404;
    }
}
