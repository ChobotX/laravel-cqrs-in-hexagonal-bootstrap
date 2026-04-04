<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\ValueObject\DefinitionName;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;

it('can be constructed with value objects', function (): void {
    $definition = new Definition(
        id: new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        namespace: new DefinitionNamespace('crm'),
        slug: new DefinitionSlug('employees'),
        name: new DefinitionName('Employee Directory'),
    );

    expect($definition->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($definition->namespace->value)->toBe('crm')
        ->and($definition->slug->value)->toBe('employees')
        ->and($definition->name->value)->toBe('Employee Directory');
});
