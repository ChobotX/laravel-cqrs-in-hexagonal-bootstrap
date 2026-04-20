<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

use function sprintf;

final class SsoLoginRejectedException extends RuntimeException implements DomainException
{
    public function __construct(public readonly string $reason)
    {
        parent::__construct(sprintf('SSO login rejected: %s', $reason));
    }

    public function userMessage(Translator $translator): string
    {
        return $translator->translate('messages.exceptions.sso_login_rejected', ['reason' => $this->reason]);
    }

    public function statusCode(): int
    {
        return HttpStatus::FORBIDDEN;
    }
}
