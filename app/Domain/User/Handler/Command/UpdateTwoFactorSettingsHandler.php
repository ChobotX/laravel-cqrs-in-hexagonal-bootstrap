<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\UpdateTwoFactorSettingsCommand;
use App\Domain\User\Contract\Repository\TwoFactorSettingsRepository;
use App\Domain\User\Contract\ValueObject\TwoFactorSettings;
use App\Domain\User\Exception\InvalidTwoFactorPolicyException;

/** @implements CommandHandler<UpdateTwoFactorSettingsCommand> */
#[SkipDomainEvent(reason: 'Tenant two-factor policy row — no domain aggregate event')]
final readonly class UpdateTwoFactorSettingsHandler implements CommandHandler
{
    public function __construct(
        private TwoFactorSettingsRepository $twoFactorSettingsRepository,
    ) {}

    public function handle(Command $command): void
    {
        if ($command->requiredForAllUsers && (! $command->emailOtpEnabled && ! $command->totpEnabled)) {
            throw new InvalidTwoFactorPolicyException('messages.exceptions.invalid_two_factor_policy_requires_method');
        }

        $this->twoFactorSettingsRepository->save(new TwoFactorSettings(
            requiredForAllUsers: $command->requiredForAllUsers,
            emailOtpEnabled: $command->emailOtpEnabled,
            totpEnabled: $command->totpEnabled,
        ));
    }
}
