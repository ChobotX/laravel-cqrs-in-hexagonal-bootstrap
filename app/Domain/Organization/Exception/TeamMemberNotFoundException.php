<?php

declare(strict_types=1);

namespace App\Domain\Organization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class TeamMemberNotFoundException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $userId,
        public readonly string $teamId,
    ) {
        parent::__construct(sprintf('User [%s] is not a member of team [%s].', $userId, $teamId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.team_member_not_found');
    }

    public function statusCode(): int
    {
        return 404;
    }
}
