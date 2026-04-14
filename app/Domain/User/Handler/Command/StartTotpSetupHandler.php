<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\StartTotpSetupCommand;
use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;
use App\Domain\User\Contract\Service\TwoFactorManager;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;

/** @implements CommandHandler<StartTotpSetupCommand> */
#[SkipDomainEvent(reason: 'Two-factor setup state mutation only')]
final readonly class StartTotpSetupHandler implements CommandHandler
{
    public function __construct(
        private UserTwoFactorStateRepository $userTwoFactorStateRepository,
        private TwoFactorManager $twoFactorManager,
        private PendingTotpBackupCodesSession $pendingTotpBackupCodesSession,
    ) {}

    public function handle(Command $command): void
    {
        $userId = new UserId($command->userId);
        $userTwoFactorState = $this->userTwoFactorStateRepository->get($userId);
        $secret = $this->twoFactorManager->generateTotpSecret();
        $plaintextRecoveryCodes = $this->twoFactorManager->generateTotpRecoveryCodes();
        $recoveryHashes = array_map(
            $this->twoFactorManager->hashTotpRecoveryCode(...),
            $plaintextRecoveryCodes,
        );

        $this->pendingTotpBackupCodesSession->forget($userId->value);
        $this->userTwoFactorStateRepository->save($userId, new UserTwoFactorState(
            emailEnabled: $userTwoFactorState->emailEnabled,
            emailConfirmedAt: $userTwoFactorState->emailConfirmedAt,
            totpSecret: $secret,
            totpConfirmedAt: null,
            totpRecoveryCodeHashes: $recoveryHashes,
        ));
        $this->pendingTotpBackupCodesSession->remember($userId->value, $plaintextRecoveryCodes);
    }
}
