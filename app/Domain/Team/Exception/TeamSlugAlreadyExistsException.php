<?php

declare(strict_types=1);

namespace App\Domain\Team\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use RuntimeException;

final class TeamSlugAlreadyExistsException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $slug)
    {
        parent::__construct(sprintf('A team with slug [%s] already exists in this organization.', $slug));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.team_slug_already_exists', ['slug' => $this->slug]);
    }

    public function statusCode(): int
    {
        return 409;
    }
}
