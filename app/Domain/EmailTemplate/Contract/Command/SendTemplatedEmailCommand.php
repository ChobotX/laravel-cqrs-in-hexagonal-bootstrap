<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\Sensitive;
use App\Application\Bus\SkipAuditLog;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Dispatched internally by event handlers, not by user action')]
#[SkipAuditLog(reason: 'Internally dispatched email sending, logged via TemplatedEmailSent event')]
#[SkipTransaction(reason: 'No database writes besides email log, sends external email')]
final readonly class SendTemplatedEmailCommand implements Command
{
    /**
     * @param  array<string, string|null>  $variables
     */
    public function __construct(
        public string $userId,
        public string $templateType,
        public string $locale,
        #[Sensitive]
        public array $variables,
    ) {}
}
