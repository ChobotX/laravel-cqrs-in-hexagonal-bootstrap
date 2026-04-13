<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Contract\Service\TemplatedEmailDispatcher;
use App\Domain\User\Contract\Command\RequestPasswordResetCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\PasswordResetRequested;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use DateTimeImmutable;

/** @implements CommandHandler<RequestPasswordResetCommand> */
final readonly class RequestPasswordResetHandler implements CommandHandler
{
    public function __construct(
        private PasswordResetBroker $passwordResetBroker,
        private UserRepository $userRepository,
        private TemplatedEmailDispatcher $templatedEmailDispatcher,
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

        if (! $user instanceof User) {
            return;
        }

        $locale = $this->translator->locale();

        $this->eventCollector->collect($this->templatedEmailDispatcher->dispatch(
            $user->id->value,
            'password_reset',
            $locale,
            ['link' => $resetLink],
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
