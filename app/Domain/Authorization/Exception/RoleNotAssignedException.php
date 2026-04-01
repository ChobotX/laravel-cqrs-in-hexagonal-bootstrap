<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class RoleNotAssignedException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $userId,
        public readonly string $roleId,
    ) {
        parent::__construct(sprintf('Role [%s] is not assigned to user [%s].', $roleId, $userId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.role_not_assigned');
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}
