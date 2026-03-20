<?php

declare(strict_types=1);

namespace App\Domain\Organization\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class TeamCycleDetectedException extends RuntimeException implements DomainException
{
    public function __construct(
        public readonly string $teamId,
        public readonly string $parentTeamId,
    ) {
        parent::__construct(sprintf('Setting parent [%s] for team [%s] would create a cycle.', $parentTeamId, $teamId));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.team_cycle_detected');
    }

    public function statusCode(): int
    {
        return 422;
    }
}
