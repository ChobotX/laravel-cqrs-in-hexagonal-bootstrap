<?php

declare(strict_types=1);

namespace App\Domain\User\Command\SetPassword;

use App\Contract\Auth\PasswordManager;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;

/** @implements CommandHandler<SetPasswordCommand> */
final readonly class SetPasswordHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordManager $passwordManager,
    ) {}

    public function handle(Command $command): void
    {
        $user = $this->userRepository->findById(new UserId($command->userId));

        if (! $user instanceof User) {
            throw new UserNotFoundException($command->userId);
        }

        $this->passwordManager->setPassword($user->id->value, $command->rawPassword);
    }
}
