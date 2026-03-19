<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\SeedDefaultRoles\SeedDefaultRolesCommand;
use App\Domain\Organization\Event\OrganizationCreated;
use App\Infrastructure\Organization\EventHandler\SeedDefaultRolesOnOrganizationCreated;
use Tests\Helper\FakeCommandBus;

it('dispatches SeedDefaultRolesCommand on OrganizationCreated', function (): void {
    $commandBus = new FakeCommandBus;
    $handler = new SeedDefaultRolesOnOrganizationCreated($commandBus);

    $event = new OrganizationCreated(
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Acme Corp',
        slug: 'acme-corp',
        occurredAt: new DateTimeImmutable('2025-01-15T10:00:00+00:00'),
    );

    $handler->handle($event);

    expect($commandBus->dispatched)->toHaveCount(1);

    $command = $commandBus->dispatched[0];
    assert($command instanceof SeedDefaultRolesCommand);

    expect($command->organizationId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
