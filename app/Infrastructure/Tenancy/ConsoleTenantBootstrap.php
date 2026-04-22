<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Contract\Attribute\TenantAwareCommand;
use App\Domain\Tenancy\Contract\Service\TenantBootstrapper;
use Illuminate\Console\Events\CommandStarting;
use ReflectionClass;

final readonly class ConsoleTenantBootstrap
{
    public function __construct(
        private TenantBootstrapper $tenantBootstrapper,
    ) {}

    public function handle(CommandStarting $commandStarting): void
    {
        $commandClass = $commandStarting->command;

        if (! class_exists($commandClass)) {
            return;
        }

        $reflectionClass = new ReflectionClass($commandClass);

        if ($reflectionClass->getAttributes(TenantAwareCommand::class) === []) {
            return;
        }

        /** @var string|null $tenantSlug */
        $tenantSlug = $commandStarting->input->getOption('tenant');

        if ($tenantSlug === null || $tenantSlug === '') {
            throw new MissingTenantOptionException($commandClass);
        }

        $this->tenantBootstrapper->bootstrapBySlug($tenantSlug);
    }
}
