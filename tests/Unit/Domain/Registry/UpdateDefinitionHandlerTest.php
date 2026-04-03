<?php

declare(strict_types=1);

use App\Domain\Registry\Command\UpdateDefinition\UpdateDefinitionCommand;
use App\Domain\Registry\Command\UpdateDefinition\UpdateDefinitionHandler;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\Event\DefinitionUpdated;
use App\Domain\Registry\Definition;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use Tests\Helper\FakeDefinitionRepository;
use Tests\Helper\FakeEventCollector;

it('updates the definition name', function (): void {
    $existing = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Old Name'),
    );

    $repository = new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateDefinitionHandler($repository, $eventCollector);

    $handler->handle(new UpdateDefinitionCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'New Name',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->name->value)->toBe('New Name')
        ->and($repository->saved[0]->namespace->value)->toBe('crm')
        ->and($repository->saved[0]->slug->value)->toBe('employees');
});

it('collects a DefinitionUpdated event', function (): void {
    $existing = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Old Name'),
    );

    $repository = new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateDefinitionHandler($repository, $eventCollector);

    $handler->handle(new UpdateDefinitionCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'New Name',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(DefinitionUpdated::class);
    assert($eventCollector->collected[0] instanceof DefinitionUpdated);
    expect($eventCollector->collected[0]->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->name)->toBe('New Name');
});

it('throws when definition not found', function (): void {
    $repository = new FakeDefinitionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateDefinitionHandler($repository, $eventCollector);

    $handler->handle(new UpdateDefinitionCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'New Name',
    ));
})->throws(DefinitionNotFoundException::class, 'Definition [550e8400-e29b-41d4-a716-446655440000] not found.');
