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
use App\Domain\User\Contract\Command\RequestPasswordResetCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\PasswordResetRequested;
use App\Domain\User\Contract\Query\GetUserByEmailQuery;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use DateTimeImmutable;

/** @implements CommandHandler<RequestPasswordResetCommand> */
final readonly class RequestPasswordResetHandler implements CommandHandler
{
    public function __construct(
        private PasswordResetBroker $passwordResetBroker,
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private EventCollector $eventCollector,
        private Translator $translator,
    ) {}

    public function handle(Command $command): void
    {
        $resetLink = $this->passwordResetBroker->createResetLink($command->email);

        if ($resetLink === null) {
            return;
        }

        /** @var ?User $user */
        $user = $this->queryBus->dispatch(new GetUserByEmailQuery(email: $command->email));

        if (! $user instanceof User) {
            return;
        }

        $locale = $this->translator->locale();

        $this->commandBus->dispatch(new SendTemplatedEmailCommand(
            userId: $user->id->value,
            templateType: 'password_reset',
            locale: $locale,
            variables: ['link' => $resetLink],
        ));

        $this->eventCollector->collect(new PasswordResetRequested(
            userId: $user->id->value,
            email: $command->email,
            resetLink: $resetLink,
            locale: $locale,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
