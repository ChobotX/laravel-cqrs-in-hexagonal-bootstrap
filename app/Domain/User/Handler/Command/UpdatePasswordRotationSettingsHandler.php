<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\UpdatePasswordRotationSettingsCommand;
use App\Domain\User\Contract\Repository\PasswordRotationSettingsRepository;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use App\Domain\User\Exception\InvalidPasswordRotationPolicyException;

/** @implements CommandHandler<UpdatePasswordRotationSettingsCommand> */
#[SkipDomainEvent(reason: 'Tenant password policy row — no domain aggregate event')]
final readonly class UpdatePasswordRotationSettingsHandler implements CommandHandler
{
    public function __construct(
        private PasswordRotationSettingsRepository $passwordRotationSettingsRepository,
    ) {}

    public function handle(Command $command): void
    {
        if ($command->rotationEnabled && ($command->maxAgeDays === null || $command->maxAgeDays < PasswordRotationSettings::MIN_PASSWORD_AGE_DAYS || $command->maxAgeDays > PasswordRotationSettings::MAX_PASSWORD_AGE_DAYS)) {
            throw new InvalidPasswordRotationPolicyException('messages.exceptions.invalid_password_rotation_max_age');
        }

        if ($command->historyCount < PasswordRotationSettings::MIN_HISTORY_COUNT || $command->historyCount > PasswordRotationSettings::MAX_HISTORY_COUNT) {
            throw new InvalidPasswordRotationPolicyException('messages.exceptions.invalid_password_rotation_history');
        }

        $this->passwordRotationSettingsRepository->save(new PasswordRotationSettings(
            rotationEnabled: $command->rotationEnabled,
            maxAgeDays: $command->rotationEnabled ? $command->maxAgeDays : null,
            historyCount: $command->historyCount,
        ));
    }
}
