<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Translation\Translator;
use App\Domain\User\Contract\Command\IssueEmailTwoFactorChallengeCommand;
use App\Domain\User\Contract\Repository\EmailTwoFactorChallengeRepository;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\TwoFactorCodeNotifier;
use App\Domain\User\Contract\Service\TwoFactorManager;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements CommandHandler<IssueEmailTwoFactorChallengeCommand> */
#[SkipDomainEvent(reason: 'Two-factor challenge row and notification only')]
final readonly class IssueEmailTwoFactorChallengeHandler implements CommandHandler
{
    private const int CHALLENGE_TTL_MINUTES = 10;

    private const string SUBJECT_KEY = 'messages.auth.two_factor_email_subject';

    private const string BODY_KEY = 'messages.auth.two_factor_email_code';

    public function __construct(
        private EmailTwoFactorChallengeRepository $emailTwoFactorChallengeRepository,
        private UserRepository $userRepository,
        private TwoFactorManager $twoFactorManager,
        private TwoFactorCodeNotifier $twoFactorCodeNotifier,
        private Translator $translator,
    ) {}

    public function handle(Command $command): void
    {
        $user = $this->userRepository->findById(new UserId($command->userId));

        if (! $user instanceof \App\Domain\User\Contract\Entity\User) {
            return;
        }

        $code = $this->twoFactorManager->generateEmailCode();
        $hash = $this->twoFactorManager->hashChallengeCode($code);
        $expiresAt = (new DateTimeImmutable)->modify('+'.self::CHALLENGE_TTL_MINUTES.' minutes');

        $this->emailTwoFactorChallengeRepository->issue(new UserId($command->userId), $hash, $expiresAt);
        $this->twoFactorCodeNotifier->send(
            $user->email->value,
            $this->translator->translate(self::SUBJECT_KEY),
            $this->translator->translate(self::BODY_KEY, ['code' => $code]),
        );
    }
}
