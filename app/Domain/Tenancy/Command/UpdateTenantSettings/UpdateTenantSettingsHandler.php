<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Command\UpdateTenantSettings;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Tenancy\Exception\InvalidTenantNameException;
use App\Domain\Tenancy\Exception\TenantNotFoundException;
use App\Domain\Tenancy\TenantSettings;
use App\Domain\Tenancy\TenantSettingsRepository;

/** @implements CommandHandler<UpdateTenantSettingsCommand> */
final readonly class UpdateTenantSettingsHandler implements CommandHandler
{
    public function __construct(
        private TenantSettingsRepository $tenantSettingsRepository,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->tenantSettingsRepository->findByTenantId($command->tenantId);

        if (! $existing instanceof TenantSettings) {
            throw new TenantNotFoundException($command->tenantId);
        }

        if (trim($command->name) === '') {
            throw new InvalidTenantNameException;
        }

        $this->tenantSettingsRepository->updateSettings(
            $command->tenantId,
            $command->name,
            $command->logo,
            $command->removeLogo,
        );
    }
}
