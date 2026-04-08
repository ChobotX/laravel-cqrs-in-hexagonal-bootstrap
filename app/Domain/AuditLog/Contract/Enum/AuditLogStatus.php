<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Enum;

enum AuditLogStatus: string
{
    case Success = 'success';
    case Failure = 'failure';
}
