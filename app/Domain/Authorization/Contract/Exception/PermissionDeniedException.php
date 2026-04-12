<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

/**
 * Thrown when the authenticated subject lacks a required permission (optional key for logging or messages).
 */
final class PermissionDeniedException extends RuntimeException implements DomainException
{
    public function __construct(
        /** Permission string that was denied (e.g. `users.edit`); empty when the failure is intentionally generic. */
        public readonly string $permission = '',
    ) {
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
