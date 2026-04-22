<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\User\Contract\Command\ConfirmTotpSetupCommand;
use App\Domain\User\Contract\Command\DisableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\DisableTotpTwoFactorCommand;
use App\Domain\User\Contract\Command\EnableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\ManageOwnTwoFactorSettingsCommand;
use App\Domain\User\Contract\Command\StartTotpSetupCommand;
use App\Domain\User\Contract\Enum\TwoFactorSettingsAction;
use App\Domain\User\Contract\Query\GetTotpSetupQuery;
use App\Domain\User\Contract\ValueObject\TotpSetup;
use App\Domain\User\Exception\InvalidTwoFactorSettingsActionPayloadException;

/** @implements CommandHandler<ManageOwnTwoFactorSettingsCommand> */
#[SkipDomainEvent(reason: 'Orchestrator — inner two-factor handlers emit events')]
final readonly class ManageOwnTwoFactorSettingsHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    public function handle(Command $command): void
    {
        match ($command->action) {
            TwoFactorSettingsAction::EmailSave => $this->applyEmail($command),
            TwoFactorSettingsAction::TotpSave => $this->applyTotpToggle($command),
            TwoFactorSettingsAction::TotpConfirm => $this->commandBus->dispatch(new ConfirmTotpSetupCommand(
                $command->userId,
                $this->requireTotpCode($command->totpCode),
            )),
            TwoFactorSettingsAction::TotpDisable => $this->commandBus->dispatch(new DisableTotpTwoFactorCommand($command->userId)),
        };
    }

    private function applyEmail(ManageOwnTwoFactorSettingsCommand $manageOwnTwoFactorSettingsCommand): void
    {
        if ($manageOwnTwoFactorSettingsCommand->emailEnabled === null) {
            throw new InvalidTwoFactorSettingsActionPayloadException(
                TwoFactorSettingsAction::EmailSave->value,
                'emailEnabled',
            );
        }

        $this->commandBus->dispatch($manageOwnTwoFactorSettingsCommand->emailEnabled
            ? new EnableEmailTwoFactorCommand($manageOwnTwoFactorSettingsCommand->userId)
            : new DisableEmailTwoFactorCommand($manageOwnTwoFactorSettingsCommand->userId));
    }

    private function applyTotpToggle(ManageOwnTwoFactorSettingsCommand $manageOwnTwoFactorSettingsCommand): void
    {
        if ($manageOwnTwoFactorSettingsCommand->totpEnabled === null) {
            throw new InvalidTwoFactorSettingsActionPayloadException(
                TwoFactorSettingsAction::TotpSave->value,
                'totpEnabled',
            );
        }

        /** @var TotpSetup $totpSetup */
        $totpSetup = $this->queryBus->dispatch(new GetTotpSetupQuery($manageOwnTwoFactorSettingsCommand->userId));

        if ($manageOwnTwoFactorSettingsCommand->totpEnabled) {
            if ($totpSetup->secret === null) {
                $this->commandBus->dispatch(new StartTotpSetupCommand($manageOwnTwoFactorSettingsCommand->userId));
            }

            return;
        }

        if ($totpSetup->secret !== null) {
            $this->commandBus->dispatch(new DisableTotpTwoFactorCommand($manageOwnTwoFactorSettingsCommand->userId));
        }
    }

    private function requireTotpCode(?string $code): string
    {
        if ($code === null || $code === '') {
            throw new InvalidTwoFactorSettingsActionPayloadException(
                TwoFactorSettingsAction::TotpConfirm->value,
                'totpCode',
            );
        }

        return $code;
    }
}
