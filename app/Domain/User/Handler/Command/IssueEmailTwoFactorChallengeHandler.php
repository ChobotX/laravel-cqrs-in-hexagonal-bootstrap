<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Contract\Service\TemplatedEmailDispatcher;
use App\Domain\User\Contract\Command\IssueEmailTwoFactorChallengeCommand;
use App\Domain\User\Contract\Repository\EmailTwoFactorChallengeRepository;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\TwoFactorManager;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements CommandHandler<IssueEmailTwoFactorChallengeCommand> */
final readonly class IssueEmailTwoFactorChallengeHandler implements CommandHandler
{
    private const int CHALLENGE_TTL_MINUTES = 10;

    private const string TEMPLATE_TYPE = 'two_factor_challenge';

    public function __construct(
        private EmailTwoFactorChallengeRepository $emailTwoFactorChallengeRepository,
        private UserRepository $userRepository,
        private TwoFactorManager $twoFactorManager,
        private TemplatedEmailDispatcher $templatedEmailDispatcher,
        private EventCollector $eventCollector,
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

        $this->eventCollector->collect($this->templatedEmailDispatcher->dispatch(
            $user->id->value,
            self::TEMPLATE_TYPE,
            $this->translator->locale(),
            ['code' => $code],
        ));
    }
}
