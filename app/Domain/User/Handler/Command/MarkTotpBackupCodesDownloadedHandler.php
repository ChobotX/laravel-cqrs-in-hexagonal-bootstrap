<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\MarkTotpBackupCodesDownloadedCommand;
use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;

/** @implements CommandHandler<MarkTotpBackupCodesDownloadedCommand> */
#[SkipDomainEvent(reason: 'Session flag for TOTP backup download only')]
final readonly class MarkTotpBackupCodesDownloadedHandler implements CommandHandler
{
    public function __construct(
        private PendingTotpBackupCodesSession $pendingTotpBackupCodesSession,
    ) {}

    public function handle(Command $command): void
    {
        /* @var MarkTotpBackupCodesDownloadedCommand $command */
        $this->pendingTotpBackupCodesSession->markDownloadRecorded($command->userId);
    }
}
