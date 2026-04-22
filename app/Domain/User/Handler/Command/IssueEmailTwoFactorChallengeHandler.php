<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\User\Contract\Command\IssueEmailTwoFactorChallengeCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Contract\Repository\EmailTwoFactorChallengeRepository;
use App\Domain\User\Contract\Service\TwoFactorManager;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements CommandHandler<IssueEmailTwoFactorChallengeCommand> */
#[SkipDomainEvent(reason: 'Challenge issuance delegates to SendTemplatedEmailCommand which emits its own TemplatedEmailSent event')]
final readonly class IssueEmailTwoFactorChallengeHandler implements CommandHandler
{
    private const int CHALLENGE_TTL_MINUTES = 10;

    private const string TEMPLATE_TYPE = 'two_factor_challenge';

    public function __construct(
        private EmailTwoFactorChallengeRepository $emailTwoFactorChallengeRepository,
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private TwoFactorManager $twoFactorManager,
        private Translator $translator,
    ) {}

    public function handle(Command $command): void
    {
        try {
            /** @var User $user */
            $user = $this->queryBus->dispatch(new GetUserByIdQuery(id: $command->userId));
        } catch (UserNotFoundException) {
            // @silent: 2FA challenge issued to authenticated session; missing user means stale session, silently no-op to avoid enumeration.
            return;
        }

        $code = $this->twoFactorManager->generateEmailCode();
        $hash = $this->twoFactorManager->hashChallengeCode($code);
        $expiresAt = (new DateTimeImmutable)->modify('+'.self::CHALLENGE_TTL_MINUTES.' minutes');

        $this->emailTwoFactorChallengeRepository->issue(new UserId($command->userId), $hash, $expiresAt);

        $this->commandBus->dispatch(new SendTemplatedEmailCommand(
            userId: $user->id->value,
            templateType: self::TEMPLATE_TYPE,
            locale: $this->translator->locale(),
            variables: ['code' => $code],
        ));
    }
}
