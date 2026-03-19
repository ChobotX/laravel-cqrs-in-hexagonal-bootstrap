<?php

declare(strict_types=1);

use App\Domain\Organization\Command\DeleteOrganization\DeleteOrganizationCommand;
use App\Domain\Organization\Command\DeleteOrganization\DeleteOrganizationHandler;
use App\Domain\Organization\Event\OrganizationDeleted;
use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeOrganizationRepository;

it('deletes an organization and emits event', function (): void {
    $org = new Organization(
        new OrganizationId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationName('Acme Corp'),
        new OrganizationSlug('acme-corp'),
        'A test org',
    );

    $repository = new FakeOrganizationRepository(['550e8400-e29b-41d4-a716-446655440000' => $org]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteOrganizationHandler($repository, $eventCollector);

    $handler->handle(new DeleteOrganizationCommand(id: '550e8400-e29b-41d4-a716-446655440000'));

    expect($repository->deleted)->toBe(['550e8400-e29b-41d4-a716-446655440000'])
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(OrganizationDeleted::class);
});

it('throws when organization not found', function (): void {
    $repository = new FakeOrganizationRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteOrganizationHandler($repository, $eventCollector);

    $handler->handle(new DeleteOrganizationCommand(id: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(OrganizationNotFoundException::class);
