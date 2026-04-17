<?php

declare(strict_types=1);

use App\Application\Tenancy\TenantAwareCommand;
use App\Domain\Tenancy\Contract\Service\TenantBootstrapper;
use App\Infrastructure\Tenancy\ConsoleTenantBootstrap;
use App\Infrastructure\Tenancy\MissingTenantOptionException;
use Illuminate\Console\Events\CommandStarting;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;

uses()->group('unit');

function createEventForConsole(string $commandClass, ?string $tenantValue = null): CommandStarting
{
    $params = $tenantValue !== null ? ['--tenant' => $tenantValue] : [];
    $input = new ArrayInput($params);
    $input->bind(new InputDefinition([
        new InputOption('tenant', null, InputOption::VALUE_OPTIONAL),
    ]));

    return new CommandStarting($commandClass, $input, new NullOutput);
}

it('skips when command class does not exist', function (): void {
    $fake = new FakeBootstrapper;
    $bootstrap = new ConsoleTenantBootstrap($fake);

    $bootstrap->handle(new CommandStarting(
        'App\\NonExistent\\FakeCommand',
        new ArrayInput([]),
        new NullOutput,
    ));

    expect($fake->bootstrappedSlug)->toBeNull();
});

it('skips when command does not have TenantAwareCommand attribute', function (): void {
    $fake = new FakeBootstrapper;
    $bootstrap = new ConsoleTenantBootstrap($fake);

    $commandClass = (new class {})::class;

    $bootstrap->handle(new CommandStarting($commandClass, new ArrayInput([]), new NullOutput));

    expect($fake->bootstrappedSlug)->toBeNull();
});

it('throws MissingTenantOptionException when --tenant is missing', function (): void {
    $fake = new FakeBootstrapper;
    $bootstrap = new ConsoleTenantBootstrap($fake);

    $commandClass = (new #[TenantAwareCommand] class {})::class;

    $bootstrap->handle(createEventForConsole($commandClass));
})->throws(MissingTenantOptionException::class, 'requires --tenant option');

it('throws MissingTenantOptionException when --tenant is empty string', function (): void {
    $fake = new FakeBootstrapper;
    $bootstrap = new ConsoleTenantBootstrap($fake);

    $commandClass = (new #[TenantAwareCommand] class {})::class;

    $bootstrap->handle(createEventForConsole($commandClass, ''));
})->throws(MissingTenantOptionException::class, 'requires --tenant option');

it('bootstraps by slug when command has TenantAwareCommand and --tenant option', function (): void {
    $fake = new FakeBootstrapper;
    $bootstrap = new ConsoleTenantBootstrap($fake);

    $commandClass = (new #[TenantAwareCommand] class {})::class;

    $bootstrap->handle(createEventForConsole($commandClass, 'alpha'));

    expect($fake->bootstrappedSlug)->toBe('alpha');
});

final class FakeBootstrapper implements TenantBootstrapper
{
    public ?string $bootstrappedSlug = null;

    public ?string $bootstrappedDomain = null;

    public function bootstrapByDomain(string $domain): void
    {
        $this->bootstrappedDomain = $domain;
    }

    public function bootstrapBySlug(string $slug): void
    {
        $this->bootstrappedSlug = $slug;
    }

    public function reset(): void {}
}
