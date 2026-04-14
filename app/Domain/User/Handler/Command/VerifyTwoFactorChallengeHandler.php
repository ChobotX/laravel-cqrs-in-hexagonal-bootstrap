<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\VerifyTwoFactorChallengeCommand;
use App\Domain\User\Contract\Repository\EmailTwoFactorChallengeRepository;
use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\Service\TwoFactorManager;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Exception\InvalidTwoFactorCodeException;
use DateTimeImmutable;

/** @implements CommandHandler<VerifyTwoFactorChallengeCommand> */
#[SkipDomainEvent(reason: 'Two-factor challenge verification only')]
final readonly class VerifyTwoFactorChallengeHandler implements CommandHandler
{
    private const string METHOD_TOTP = 'totp';

    public function __construct(
        private UserTwoFactorStateRepository $userTwoFactorStateRepository,
        private EmailTwoFactorChallengeRepository $emailTwoFactorChallengeRepository,
        private TwoFactorManager $twoFactorManager,
    ) {}

    public function handle(Command $command): void
    {
        $userId = new UserId($command->userId);
        $userTwoFactorState = $this->userTwoFactorStateRepository->get($userId);

        if ($command->method === self::METHOD_TOTP) {
            if ($userTwoFactorState->totpSecret === null || ! $this->twoFactorManager->verifyTotpCode($userTwoFactorState->totpSecret, $command->code)) {
                throw new InvalidTwoFactorCodeException;
            }

            return;
        }

        $challenge = $this->emailTwoFactorChallengeRepository->latest($userId);
        $now = new DateTimeImmutable;

        if (! $challenge instanceof \App\Domain\User\Contract\ValueObject\EmailTwoFactorChallenge || $challenge->consumed || $challenge->isExpired($now) || ! $this->twoFactorManager->verifyChallengeCode($command->code, $challenge->codeHash)) {
            $this->emailTwoFactorChallengeRepository->markAttempt($userId);

            throw new InvalidTwoFactorCodeException;
        }

        $this->emailTwoFactorChallengeRepository->consume($userId);
    }
}
