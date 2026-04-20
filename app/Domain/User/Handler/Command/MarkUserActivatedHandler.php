<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\User\Contract\Command\MarkUserActivatedCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteAccepted;
use App\Domain\User\Contract\Exception\UserAlreadyActivatedException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements CommandHandler<MarkUserActivatedCommand> */
final readonly class MarkUserActivatedHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->userRepository->findById(new UserId($command->userId));

        if (! $existing instanceof User) {
            throw new UserNotFoundException($command->userId);
        }

        if ($existing->isActivated) {
            throw new UserAlreadyActivatedException($existing->id->value);
        }

        $user = new User(
            id: $existing->id,
            name: $existing->name,
            email: $existing->email,
            isActivated: true,
            avatarFileId: $existing->avatarFileId,
            passwordChangedAt: $existing->passwordChangedAt,
            emailTwoFactorEnabled: $existing->emailTwoFactorEnabled,
            emailTwoFactorConfirmedAt: $existing->emailTwoFactorConfirmedAt,
            totpSecret: $existing->totpSecret,
            totpConfirmedAt: $existing->totpConfirmedAt,
        );

        $this->userRepository->update($user);

        $this->eventCollector->collect(new UserInviteAccepted(
            userId: $existing->id->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
