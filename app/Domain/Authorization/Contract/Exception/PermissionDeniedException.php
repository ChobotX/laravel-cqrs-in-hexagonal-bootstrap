<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class PermissionDeniedException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $permission = '')
    {
        parent::__construct(
            $permission !== ''
                ? sprintf('Permission denied: %s.', $permission)
                : 'Permission denied.',
        );
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.permission_denied');
    }

    public function statusCode(): int
    {
        return HttpStatus::FORBIDDEN;
    }
}
