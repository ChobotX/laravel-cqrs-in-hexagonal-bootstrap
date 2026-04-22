<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\User\Contract\Command\ResendUserInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteSent;
use App\Domain\User\Contract\Exception\UserAlreadyActivatedException;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Contract\Service\InviteLinkGenerator;
use DateTimeImmutable;

/** @implements CommandHandler<ResendUserInviteCommand> */
final readonly class ResendUserInviteHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private InviteLinkGenerator $inviteLinkGenerator,
        private EventCollector $eventCollector,
        private Translator $translator,
    ) {}

    public function handle(Command $command): void
    {
        /** @var User $user */
        $user = $this->queryBus->dispatch(new GetUserByIdQuery(id: $command->userId));

        if ($user->isActivated) {
            throw new UserAlreadyActivatedException($user->id->value);
        }

        $inviteLink = $this->inviteLinkGenerator->generate($user->id->value);
        $locale = $this->translator->locale();

        $this->commandBus->dispatch(new SendTemplatedEmailCommand(
            userId: $user->id->value,
            templateType: 'user_invite',
            locale: $locale,
            variables: ['userName' => $user->name->value, 'link' => $inviteLink],
        ));

        $this->eventCollector->collect(new UserInviteSent(
            userId: $user->id->value,
            userName: $user->name->value,
            inviteLink: $inviteLink,
            locale: $locale,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
