<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\AdminResetUserTwoFactorCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Repository\EmailTwoFactorChallengeRepository;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\Service\PendingTotpBackupCodesSession;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;

/** @implements CommandHandler<AdminResetUserTwoFactorCommand> */
#[SkipDomainEvent(reason: 'Administrative two-factor state reset only')]
final readonly class AdminResetUserTwoFactorHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserTwoFactorStateRepository $userTwoFactorStateRepository,
        private EmailTwoFactorChallengeRepository $emailTwoFactorChallengeRepository,
        private PendingTotpBackupCodesSession $pendingTotpBackupCodesSession,
    ) {}

    public function handle(Command $command): void
    {
        $userId = new UserId($command->targetUserId);
        $user = $this->userRepository->findById($userId);

        if (! $user instanceof User) {
            throw new UserNotFoundException($command->targetUserId);
        }

        $this->emailTwoFactorChallengeRepository->deleteAllForUser($userId);
        $this->pendingTotpBackupCodesSession->forget($userId->value);
        $this->userTwoFactorStateRepository->save($userId, new UserTwoFactorState(
            emailEnabled: false,
            emailConfirmedAt: null,
            totpSecret: null,
            totpConfirmedAt: null,
        ));
    }
}
