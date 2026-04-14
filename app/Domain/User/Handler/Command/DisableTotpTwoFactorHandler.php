<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\DisableTotpTwoFactorCommand;
use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;

/** @implements CommandHandler<DisableTotpTwoFactorCommand> */
#[SkipDomainEvent(reason: 'Two-factor setup state mutation only')]
final readonly class DisableTotpTwoFactorHandler implements CommandHandler
{
    public function __construct(
        private UserTwoFactorStateRepository $userTwoFactorStateRepository,
        private PendingTotpBackupCodesSession $pendingTotpBackupCodesSession,
    ) {}

    public function handle(Command $command): void
    {
        $userId = new UserId($command->userId);
        $userTwoFactorState = $this->userTwoFactorStateRepository->get($userId);

        $this->pendingTotpBackupCodesSession->forget($userId->value);
        $this->userTwoFactorStateRepository->save($userId, new UserTwoFactorState(
            emailEnabled: $userTwoFactorState->emailEnabled,
            emailConfirmedAt: $userTwoFactorState->emailConfirmedAt,
            totpSecret: null,
            totpConfirmedAt: null,
        ));
    }
}
