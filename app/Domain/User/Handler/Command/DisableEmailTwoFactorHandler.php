<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\DisableEmailTwoFactorCommand;
use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;

/** @implements CommandHandler<DisableEmailTwoFactorCommand> */
#[SkipDomainEvent(reason: 'Two-factor setup state mutation only')]
final readonly class DisableEmailTwoFactorHandler implements CommandHandler
{
    public function __construct(
        private UserTwoFactorStateRepository $userTwoFactorStateRepository,
    ) {}

    public function handle(Command $command): void
    {
        $userId = new UserId($command->userId);
        $userTwoFactorState = $this->userTwoFactorStateRepository->get($userId);

        $this->userTwoFactorStateRepository->save($userId, new UserTwoFactorState(
            emailEnabled: false,
            emailConfirmedAt: null,
            totpSecret: $userTwoFactorState->totpSecret,
            totpConfirmedAt: $userTwoFactorState->totpConfirmedAt,
            totpRecoveryCodeHashes: $userTwoFactorState->totpRecoveryCodeHashes,
        ));
    }
}
