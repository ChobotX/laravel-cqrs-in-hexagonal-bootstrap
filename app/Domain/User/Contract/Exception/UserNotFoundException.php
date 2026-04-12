<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

/**
 * Thrown when a user id does not resolve to an existing user visible in the current context.
 */
final class UserNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(
        /** User id that was looked up (UUID). */
        public readonly string $id,
    ) {
        parent::__construct(sprintf('User with id [%s] not found.', $id));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.user_not_found', ['id' => $this->id]);
    }

    public function statusCode(): int
    {
        return HttpStatus::NOT_FOUND;
    }
}
