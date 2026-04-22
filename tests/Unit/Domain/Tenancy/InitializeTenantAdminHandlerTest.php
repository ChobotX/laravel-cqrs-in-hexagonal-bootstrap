<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Command\AssignRoleToUserCommand;
use App\Domain\Authorization\Contract\Command\CreateSystemRoleCommand;
use App\Domain\Authorization\Contract\Command\SeedDefaultRolesCommand;
use App\Domain\EmailTemplate\Contract\Command\SeedDefaultEmailTemplatesCommand;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Handler\Command\InitializeTenantAdminHandler;
use App\Domain\User\Contract\Command\CreateUserCommand;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeTenantBootstrapper;

it('bootstraps tenant and orchestrates tenant admin setup in handler', function (): void {
    $commandBus = new FakeCommandBus;
    $idGenerator = new FakeIdGenerator;
    $bootstrapper = new FakeTenantBootstrapper;
    $handler = new InitializeTenantAdminHandler(
        $bootstrapper,
        $commandBus,
        $idGenerator,
    );

    $handler->handle(new InitializeTenantAdminCommand(
        tenantSlug: 'test-tenant',
        adminId: '00000000-0000-0000-0000-000000000099',
        adminName: 'Jane Admin',
        adminEmail: 'jane@example.com',
    ));

    expect($bootstrapper->bootstrappedSlug)->toBe('test-tenant')
        ->and($commandBus->dispatched)->toHaveCount(5)
        ->and($commandBus->dispatched[0])->toBeInstanceOf(SeedDefaultEmailTemplatesCommand::class)
        ->and($commandBus->dispatched[1])->toBeInstanceOf(SeedDefaultRolesCommand::class)
        ->and($commandBus->dispatched[2])->toBeInstanceOf(CreateSystemRoleCommand::class)
        ->and($commandBus->dispatched[3])->toBeInstanceOf(CreateUserCommand::class)
        ->and($commandBus->dispatched[4])->toBeInstanceOf(AssignRoleToUserCommand::class);

    $createUserCommand = $commandBus->dispatched[3];
    assert($createUserCommand instanceof CreateUserCommand);
    expect($createUserCommand->id)->toBe('00000000-0000-0000-0000-000000000099')
        ->and($createUserCommand->name)->toBe('Jane Admin')
        ->and($createUserCommand->email)->toBe('jane@example.com');

    $assignRoleCommand = $commandBus->dispatched[4];
    assert($assignRoleCommand instanceof AssignRoleToUserCommand);
    $createSystemRoleCommand = $commandBus->dispatched[2];
    assert($createSystemRoleCommand instanceof CreateSystemRoleCommand);
    expect($assignRoleCommand->userId)->toBe('00000000-0000-0000-0000-000000000099')
        ->and($assignRoleCommand->roleId)->toBe($createSystemRoleCommand->id);
});
