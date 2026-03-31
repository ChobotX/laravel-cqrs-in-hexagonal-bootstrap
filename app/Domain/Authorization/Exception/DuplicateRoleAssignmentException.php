<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class DuplicateRoleAssignmentException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $userId,
        public readonly string $roleId,
    ) {
        parent::__construct(sprintf('Role [%s] is already assigned to user [%s].', $roleId, $userId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.duplicate_role_assignment');
    }

    public function statusCode(): int
    {
        return HttpStatus::CONFLICT;
    }
}
