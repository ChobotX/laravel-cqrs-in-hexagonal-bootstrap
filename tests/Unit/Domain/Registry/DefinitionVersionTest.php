<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\ValueObject\VersionNumber;

it('can be constructed with value objects and schema', function (): void {
    $schema = new Schema([new StringField('name', 'Name', true)]);

    $version = new DefinitionVersion(
        id: new DefinitionVersionId('550e8400-e29b-41d4-a716-446655440000'),
        definitionId: new DefinitionId('660e8400-e29b-41d4-a716-446655440000'),
        version: new VersionNumber(1),
        schema: $schema,
        status: VersionStatus::Draft,
    );

    expect($version->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($version->definitionId->value)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($version->version->value)->toBe(1)
        ->and($version->schema)->toBe($schema)
        ->and($version->status)->toBe(VersionStatus::Draft);
});
