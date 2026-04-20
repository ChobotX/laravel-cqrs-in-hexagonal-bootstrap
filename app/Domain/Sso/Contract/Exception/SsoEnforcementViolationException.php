<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class SsoEnforcementViolationException extends RuntimeException implements DomainException
{
    public function __construct()
    {
        parent::__construct('Password login is disabled for this tenant; sign in via SSO.');
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.sso_enforcement_violation');
    }

    public function statusCode(): int
    {
        return HttpStatus::FORBIDDEN;
    }
}
