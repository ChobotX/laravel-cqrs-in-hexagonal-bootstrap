<?php

declare(strict_types=1);

use App\Domain\User\Service\DefaultTenantAdminUserSnapshotFactory;

it('builds a user aggregate from primitives', function (): void {
    $factory = new DefaultTenantAdminUserSnapshotFactory;

    $user = $factory->createFromPrimitives(
        '00000000-0000-0000-0000-000000000099',
        'Tenant Admin',
        'admin@tenant.example',
    );

    expect($user->id->value)->toBe('00000000-0000-0000-0000-000000000099')
        ->and($user->name->value)->toBe('Tenant Admin')
        ->and($user->email->value)->toBe('admin@tenant.example');
});
