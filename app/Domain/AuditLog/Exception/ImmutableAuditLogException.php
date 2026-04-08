<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Exception;

use App\Contract\Exception\DomainException;
use App\Contract\Http\HttpStatus;
use App\Contract\Translation\Translator;
use RuntimeException;

final class ImmutableAuditLogException extends RuntimeException implements DomainException
{
    public function __construct(string $operation)
    {
        parent::__construct(sprintf('Audit log entries are immutable and cannot be %s.', $operation));
    }

    public function userMessage(Translator $translator): string
    {
        return $this->getMessage();
    }

    public function statusCode(): int
    {
        return HttpStatus::FORBIDDEN;
    }
}
