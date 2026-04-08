<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class GridPresetScopePermissionException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $scope)
    {
        parent::__construct(sprintf('User lacks permission to share grid preset with scope [%s].', $scope));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.grid_preset_scope_permission');
    }

    public function statusCode(): int
    {
        return HttpStatus::FORBIDDEN;
    }
}
