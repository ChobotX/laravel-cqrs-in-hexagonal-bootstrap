<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Domain\Registry\Constant\DefinitionFields;
use App\Domain\Registry\Contract\Command\UpdateDefinitionCommand;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Event\DefinitionUpdated;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use App\Domain\Registry\Handler\Command\UpdateDefinitionHandler;
use App\Domain\Registry\ValueObject\DefinitionName;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;
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

it('collects a DefinitionUpdated event with changes', function (): void {
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
        ->and($eventCollector->collected[0]->changes())->toEqual([
            new PropertyChange(DefinitionFields::NAME, 'Old Name', 'New Name'),
        ]);
});

it('does not save or collect event when data is unchanged', function (): void {
    $existing = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Same Name'),
    );

    $repository = new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateDefinitionHandler($repository, $eventCollector);

    $handler->handle(new UpdateDefinitionCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Same Name',
    ));

    expect($repository->saved)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
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
