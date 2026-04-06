<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Translation\Translator;
use App\Domain\User\Contract\Command\RequestPasswordResetCommand;
use App\Domain\User\Contract\Event\PasswordResetRequested;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\DirectEmailSender;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use DateTimeImmutable;

/** @implements CommandHandler<RequestPasswordResetCommand> */
final readonly class RequestPasswordResetHandler implements CommandHandler
{
    public function __construct(
        private PasswordResetBroker $passwordResetBroker,
        private UserRepository $userRepository,
        private DirectEmailSender $directEmailSender,
        private EventCollector $eventCollector,
        private Translator $translator,
    ) {}

    public function handle(Command $command): void
    {
        $resetLink = $this->passwordResetBroker->createResetLink($command->email);

        if ($resetLink === null) {
            return;
        }

        $user = $this->userRepository->findByEmail($command->email);

        if (! $user instanceof \App\Domain\User\Contract\Entity\User) {
            return;
        }

        $this->directEmailSender->sendToUser(
            $user->id->value,
            $this->translator->translate('messages.email.password_reset_subject'),
            $this->translator->translate('messages.email.password_reset_body', ['link' => $resetLink]),
        );

        $this->eventCollector->collect(new PasswordResetRequested(
            email: $command->email,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
