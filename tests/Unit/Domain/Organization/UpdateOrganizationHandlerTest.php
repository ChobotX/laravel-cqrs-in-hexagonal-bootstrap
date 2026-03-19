<?php

declare(strict_types=1);

use App\Domain\Organization\Command\UpdateOrganization\UpdateOrganizationCommand;
use App\Domain\Organization\Command\UpdateOrganization\UpdateOrganizationHandler;
use App\Domain\Organization\Event\OrganizationUpdated;
use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Exception\OrganizationSlugAlreadyExistsException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationSlug;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeOrganizationRepository;

function existingOrg(): Organization
{
    return new Organization(
        new OrganizationId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationName('Acme Corp'),
        new OrganizationSlug('acme-corp'),
        'Original description',
    );
}

it('updates an organization and emits event', function (): void {
    $repository = new FakeOrganizationRepository(['550e8400-e29b-41d4-a716-446655440000' => existingOrg()]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateOrganizationHandler($repository, $eventCollector);

    $handler->handle(new UpdateOrganizationCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Updated Name',
        slug: 'updated-slug',
        description: 'Updated description',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->name->value)->toBe('Updated Name')
        ->and($repository->saved[0]->slug->value)->toBe('updated-slug')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(OrganizationUpdated::class);
});

it('throws when organization not found', function (): void {
    $repository = new FakeOrganizationRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateOrganizationHandler($repository, $eventCollector);

    $handler->handle(new UpdateOrganizationCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Name',
        slug: 'slug',
        description: '',
    ));
})->throws(OrganizationNotFoundException::class);

it('throws when slug collides with another organization', function (): void {
    $other = new Organization(
        new OrganizationId('660e8400-e29b-41d4-a716-446655440000'),
        new OrganizationName('Other'),
        new OrganizationSlug('taken-slug'),
        'Other org',
    );

    $repository = new FakeOrganizationRepository([
        '550e8400-e29b-41d4-a716-446655440000' => existingOrg(),
        '660e8400-e29b-41d4-a716-446655440000' => $other,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateOrganizationHandler($repository, $eventCollector);

    $handler->handle(new UpdateOrganizationCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Acme',
        slug: 'taken-slug',
        description: '',
    ));
})->throws(OrganizationSlugAlreadyExistsException::class);

it('allows keeping the same slug', function (): void {
    $repository = new FakeOrganizationRepository(['550e8400-e29b-41d4-a716-446655440000' => existingOrg()]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateOrganizationHandler($repository, $eventCollector);

    $handler->handle(new UpdateOrganizationCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'New Name',
        slug: 'acme-corp',
        description: 'Same slug, different name',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->slug->value)->toBe('acme-corp');
});
