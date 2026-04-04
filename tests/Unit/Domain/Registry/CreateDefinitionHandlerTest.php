<?php

declare(strict_types=1);

use App\Domain\Registry\Command\CreateDefinition\CreateDefinitionHandler;
use App\Domain\Registry\Contract\Command\CreateDefinition\CreateDefinitionCommand;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\Event\DefinitionCreated;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\Exception\DefinitionAlreadyExistsException;
use Tests\Helper\FakeDefinitionRepository;
use Tests\Helper\FakeEventCollector;

it('saves a definition via the repository', function (): void {
    $repository = new FakeDefinitionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateDefinitionHandler($repository, $eventCollector);

    $handler->handle(new CreateDefinitionCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'crm',
        slug: 'employees',
        name: 'Employee Directory',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($repository->saved[0]->namespace->value)->toBe('crm')
        ->and($repository->saved[0]->slug->value)->toBe('employees')
        ->and($repository->saved[0]->name->value)->toBe('Employee Directory');
});

it('collects a DefinitionCreated event', function (): void {
    $repository = new FakeDefinitionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateDefinitionHandler($repository, $eventCollector);

    $handler->handle(new CreateDefinitionCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'crm',
        slug: 'employees',
        name: 'Employee Directory',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(DefinitionCreated::class);
    assert($eventCollector->collected[0] instanceof DefinitionCreated);
    expect($eventCollector->collected[0]->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->namespace)->toBe('crm')
        ->and($eventCollector->collected[0]->slug)->toBe('employees')
        ->and($eventCollector->collected[0]->name)->toBe('Employee Directory')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws when definition with same namespace and slug already exists', function (): void {
    $existing = new Definition(
        new DefinitionId('660e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Existing'),
    );

    $repository = new FakeDefinitionRepository(['660e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new CreateDefinitionHandler($repository, $eventCollector);

    $handler->handle(new CreateDefinitionCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'crm',
        slug: 'employees',
        name: 'Employee Directory',
    ));
})->throws(DefinitionAlreadyExistsException::class, 'A definition [employees] already exists in namespace [crm].');
