<?php

declare(strict_types=1);

use App\Application\Tenancy\TenantAwareCommand;
use App\Contract\Tenancy\TenantBootstrapper;
use App\Infrastructure\Tenancy\ConsoleTenantBootstrap;
use Illuminate\Console\Events\CommandStarting;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

function fakeBootstrapper(): TenantBootstrapper
{
    return new class implements TenantBootstrapper
    {
        public ?string $bootstrappedSlug = null;

        public ?string $bootstrappedDomain = null;

        public bool $wasReset = false;

        public function bootstrapByDomain(string $domain): void
        {
            $this->bootstrappedDomain = $domain;
        }

        public function bootstrapBySlug(string $slug): void
        {
            $this->bootstrappedSlug = $slug;
        }

        public function reset(): void
        {
            $this->wasReset = true;
        }
    };
}

it('skips when command class does not exist', function (): void {
    $consoleTenantBootstrap = new ReflectionClass(ConsoleTenantBootstrap::class)->newInstanceWithoutConstructor();
    $event = new CommandStarting('App\\NonExistent\\FakeCommand', new ArrayInput([]), new NullOutput);
    $consoleTenantBootstrap->handle($event);
    expect(true)->toBeTrue();
});

it('skips when command does not have TenantAwareCommand attribute', function (): void {
    $consoleTenantBootstrap = new ReflectionClass(ConsoleTenantBootstrap::class)->newInstanceWithoutConstructor();
    $commandClass = (new class {})::class;
    $event = new CommandStarting($commandClass, new ArrayInput([]), new NullOutput);
    $consoleTenantBootstrap->handle($event);
    expect(true)->toBeTrue();
});

it('throws RuntimeException when --tenant is missing', function (): void {
    $consoleTenantBootstrap = new ReflectionClass(ConsoleTenantBootstrap::class)->newInstanceWithoutConstructor();
    $commandClass = (new #[TenantAwareCommand] class {})::class;
    $input = new ArrayInput([]);
    $input->bind(new Symfony\Component\Console\Input\InputDefinition([
        new Symfony\Component\Console\Input\InputOption('tenant', null, Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL),
    ]));
    $event = new CommandStarting($commandClass, $input, new NullOutput);
    $consoleTenantBootstrap->handle($event);
})->throws(RuntimeException::class, 'requires --tenant option');

it('throws RuntimeException when --tenant is empty string', function (): void {
    $consoleTenantBootstrap = new ReflectionClass(ConsoleTenantBootstrap::class)->newInstanceWithoutConstructor();
    $commandClass = (new #[TenantAwareCommand] class {})::class;
    $input = new ArrayInput(['--tenant' => '']);
    $input->bind(new Symfony\Component\Console\Input\InputDefinition([
        new Symfony\Component\Console\Input\InputOption('tenant', null, Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL),
    ]));
    $event = new CommandStarting($commandClass, $input, new NullOutput);
    $consoleTenantBootstrap->handle($event);
})->throws(RuntimeException::class, 'requires --tenant option');

it('bootstraps by slug when command has TenantAwareCommand and --tenant option', function (): void {
    $tenantBootstrapper = fakeBootstrapper();
    $bootstrap = new ConsoleTenantBootstrap($tenantBootstrapper);
    $commandClass = (new #[TenantAwareCommand] class {})::class;
    $input = new ArrayInput(['--tenant' => 'alpha']);
    $input->bind(new Symfony\Component\Console\Input\InputDefinition([
        new Symfony\Component\Console\Input\InputOption('tenant', null, Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL),
    ]));
    $event = new CommandStarting($commandClass, $input, new NullOutput);
    $bootstrap->handle($event);

    expect($tenantBootstrapper)->toHaveProperty('bootstrappedSlug', 'alpha');
});
