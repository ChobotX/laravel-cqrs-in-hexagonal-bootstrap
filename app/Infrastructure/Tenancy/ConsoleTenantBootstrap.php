<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Application\Tenancy\TenantAwareCommand;
use Illuminate\Console\Events\CommandStarting;
use ReflectionClass;

final readonly class ConsoleTenantBootstrap
{
    public function __construct(
        private TenantBootstrapperImpl $bootstrapper,
    ) {}

    public function handle(CommandStarting $event): void
    {
        $commandClass = $event->command;

        if ($commandClass === null || ! class_exists($commandClass)) {
            return;
        }

        $reflection = new ReflectionClass($commandClass);

        if ($reflection->getAttributes(TenantAwareCommand::class) === []) {
            return;
        }

        /** @var string|null $tenantSlug */
        $tenantSlug = $event->input->getOption('tenant');

        if ($tenantSlug === null || $tenantSlug === '') {
            throw new \RuntimeException(sprintf(
                'Command %s requires --tenant option (marked with #[TenantAwareCommand]).',
                $commandClass,
            ));
        }

        $this->bootstrapper->bootstrapBySlug($tenantSlug);
    }
}
