<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class GridPresetOwnershipException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $presetId)
    {
        parent::__construct(sprintf('User does not own grid preset [%s].', $presetId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.grid_preset_ownership');
    }

    public function statusCode(): int
    {
        return HttpStatus::FORBIDDEN;
    }
}
