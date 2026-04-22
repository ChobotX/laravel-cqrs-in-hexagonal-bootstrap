<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Command\CreateSystemRoleCommand;
use App\Domain\Authorization\Handler\Command\CreateSystemRoleHandler;
use Tests\Helper\FakeRoleRepository;

it('creates a system-marked role with the supplied id, name and description', function (): void {
    $roleRepository = new FakeRoleRepository;
    $handler = new CreateSystemRoleHandler($roleRepository);

    $handler->handle(new CreateSystemRoleCommand(
        id: '11111111-2222-3333-4444-555555555555',
        name: 'Super Admin',
        description: 'System super admin with all permissions',
    ));

    expect($roleRepository->saved)->toHaveCount(1);
    $role = $roleRepository->saved[0];
    expect($role->id->value)->toBe('11111111-2222-3333-4444-555555555555')
        ->and($role->name->value)->toBe('Super Admin')
        ->and($role->description)->toBe('System super admin with all permissions')
        ->and($role->isSystem)->toBeTrue()
        ->and($role->permissions)->toBe([]);
});
