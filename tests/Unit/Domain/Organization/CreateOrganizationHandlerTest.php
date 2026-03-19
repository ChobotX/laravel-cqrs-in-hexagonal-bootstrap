<?php

declare(strict_types=1);

use App\Domain\Organization\Command\CreateOrganization\CreateOrganizationCommand;
use App\Domain\Organization\Command\CreateOrganization\CreateOrganizationHandler;
use App\Domain\Organization\Event\OrganizationCreated;
use App\Domain\Organization\Exception\OrganizationSlugAlreadyExistsException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeOrganizationRepository;

it('creates an organization and emits event', function (): void {
    $repository = new FakeOrganizationRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateOrganizationHandler($repository, $eventCollector);

    $handler->handle(new CreateOrganizationCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Acme Corp',
        slug: 'acme-corp',
        description: 'A test organization',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->name->value)->toBe('Acme Corp')
        ->and($repository->saved[0]->slug->value)->toBe('acme-corp')
        ->and($repository->saved[0]->description)->toBe('A test organization')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(OrganizationCreated::class);
});

it('throws when slug already exists', function (): void {
    $existing = new Organization(
        new OrganizationId('660e8400-e29b-41d4-a716-446655440000'),
        new OrganizationName('Existing'),
        new OrganizationSlug('acme-corp'),
        'Existing org',
    );

    $repository = new FakeOrganizationRepository(['660e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new CreateOrganizationHandler($repository, $eventCollector);

    $handler->handle(new CreateOrganizationCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Another Org',
        slug: 'acme-corp',
        description: 'Duplicate slug',
    ));
})->throws(OrganizationSlugAlreadyExistsException::class);
