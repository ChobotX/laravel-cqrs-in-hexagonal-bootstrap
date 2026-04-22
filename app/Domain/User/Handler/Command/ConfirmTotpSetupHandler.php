<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\ConfirmTotpSetupCommand;
use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;
use App\Domain\User\Contract\Service\TwoFactorManager;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;
use App\Domain\User\Exception\InvalidTwoFactorCodeException;
use App\Domain\User\Exception\TotpBackupCodesDownloadRequiredException;
use DateTimeImmutable;

/** @implements CommandHandler<ConfirmTotpSetupCommand> */
#[SkipDomainEvent(reason: 'Two-factor setup state mutation only')]
final readonly class ConfirmTotpSetupHandler implements CommandHandler
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

        if (! $this->pendingTotpBackupCodesSession->hasRecordedDownload($userId->value)) {
            throw new TotpBackupCodesDownloadRequiredException;
        }

        if ($userTwoFactorState->totpSecret === null || ! $this->twoFactorManager->verifyTotpCode($userTwoFactorState->totpSecret, $command->code)) {
            throw new InvalidTwoFactorCodeException;
        }

        $this->userTwoFactorStateRepository->save($userId, new UserTwoFactorState(
            emailEnabled: $userTwoFactorState->emailEnabled,
            emailConfirmedAt: $userTwoFactorState->emailConfirmedAt,
            totpSecret: $userTwoFactorState->totpSecret,
            totpConfirmedAt: new DateTimeImmutable,
            totpRecoveryCodeHashes: $userTwoFactorState->totpRecoveryCodeHashes,
        ));
        $this->pendingTotpBackupCodesSession->forget($userId->value);
    }
}
