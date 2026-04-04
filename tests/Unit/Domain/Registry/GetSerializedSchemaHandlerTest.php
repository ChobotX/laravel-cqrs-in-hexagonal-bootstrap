<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\VersionStatus;
use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\Query\GetSerializedSchema\GetSerializedSchemaHandler;
use App\Domain\Registry\Query\GetSerializedSchema\GetSerializedSchemaQuery;
use App\Domain\Registry\Schema\JsonSchema;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\VersionNumber;
use Tests\Helper\FakeDefinitionVersionRepository;
use Tests\Helper\FakeSchemaSerializer;

it('returns serialized schema when active version exists', function (): void {
    $schema = new Schema([new StringField('name', 'Name', true)]);
    $definitionId = new DefinitionId('550e8400-e29b-41d4-a716-446655440000');

    $version = new DefinitionVersion(
        new DefinitionVersionId('660e8400-e29b-41d4-a716-446655440000'),
        $definitionId,
        new VersionNumber(1),
        $schema,
        VersionStatus::Active,
    );

    $versionRepo = new FakeDefinitionVersionRepository([
        '660e8400-e29b-41d4-a716-446655440000' => $version,
    ]);
    $serializer = new FakeSchemaSerializer(['type' => 'object', 'properties' => ['name' => ['type' => 'string']]]);

    $handler = new GetSerializedSchemaHandler($versionRepo, $serializer);

    $result = $handler->handle(new GetSerializedSchemaQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000'));

    assert($result instanceof JsonSchema);
    expect($result->encoded)->toBeString()
        ->and(json_decode($result->encoded, true))->toBe(['type' => 'object', 'properties' => ['name' => ['type' => 'string']]]);
});

it('returns null when no active version exists', function (): void {
    $versionRepo = new FakeDefinitionVersionRepository;
    $serializer = new FakeSchemaSerializer;

    $handler = new GetSerializedSchemaHandler($versionRepo, $serializer);

    $result = $handler->handle(new GetSerializedSchemaQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toBeNull();
});
