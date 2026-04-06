<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Translation\Translator;
use App\Domain\User\Contract\Command\SendUserInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteSent;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\DirectEmailSender;
use App\Domain\User\Contract\Service\InviteLinkGenerator;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements CommandHandler<SendUserInviteCommand> */
final readonly class SendUserInviteHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private InviteLinkGenerator $inviteLinkGenerator,
        private DirectEmailSender $directEmailSender,
        private EventCollector $eventCollector,
        private Translator $translator,
    ) {}

    public function handle(Command $command): void
    {
        $user = $this->userRepository->findById(new UserId($command->userId));

        if (! $user instanceof User) {
            throw new UserNotFoundException($command->userId);
        }

        $inviteLink = $this->inviteLinkGenerator->generate($user->id->value);

        $this->directEmailSender->sendToUser(
            $user->id->value,
            $this->translator->translate('messages.email.invite_subject'),
            $this->translator->translate('messages.email.invite_body', ['link' => $inviteLink]),
        );

        $this->eventCollector->collect(new UserInviteSent(
            userId: $user->id->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
