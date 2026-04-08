<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class GridPresetNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $presetId)
    {
        parent::__construct(sprintf('Grid preset [%s] not found.', $presetId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.grid_preset_not_found');
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}
