<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Contract\Service\TemplatedEmailDispatcher;
use App\Domain\User\Contract\Command\ResendUserInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteSent;
use App\Domain\User\Contract\Exception\UserAlreadyActivatedException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\InviteLinkGenerator;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements CommandHandler<ResendUserInviteCommand> */
final readonly class ResendUserInviteHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private InviteLinkGenerator $inviteLinkGenerator,
        private TemplatedEmailDispatcher $templatedEmailDispatcher,
        private EventCollector $eventCollector,
        private Translator $translator,
    ) {}

    public function handle(Command $command): void
    {
        $user = $this->userRepository->findById(new UserId($command->userId));

        if (! $user instanceof User) {
            throw new UserNotFoundException($command->userId);
        }

        if ($user->isActivated) {
            throw new UserAlreadyActivatedException($user->id->value);
        }

        $inviteLink = $this->inviteLinkGenerator->generate($user->id->value);
        $locale = $this->translator->locale();

        $this->eventCollector->collect($this->templatedEmailDispatcher->dispatch(
            $user->id->value,
            'user_invite',
            $locale,
            ['userName' => $user->name->value, 'link' => $inviteLink],
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
