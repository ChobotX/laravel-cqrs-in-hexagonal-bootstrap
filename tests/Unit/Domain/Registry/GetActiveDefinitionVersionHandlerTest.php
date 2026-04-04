<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\Query\GetActiveDefinitionVersionQuery;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\Handler\Query\GetActiveDefinitionVersionHandler;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\ValueObject\VersionNumber;
use Tests\Helper\FakeDefinitionVersionRepository;

it('returns the active version', function (): void {
    $schema = new Schema([new StringField('name', 'Name', true)]);
    $definitionId = new DefinitionId('550e8400-e29b-41d4-a716-446655440000');

    $version = new DefinitionVersion(
        new DefinitionVersionId('660e8400-e29b-41d4-a716-446655440000'),
        $definitionId,
        new VersionNumber(1),
        $schema,
        VersionStatus::Active,
    );

    $repo = new FakeDefinitionVersionRepository([
        '660e8400-e29b-41d4-a716-446655440000' => $version,
    ]);
    $handler = new GetActiveDefinitionVersionHandler($repo);

    $result = $handler->handle(new GetActiveDefinitionVersionQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000'));

    assert($result instanceof DefinitionVersion);
    expect($result->status)->toBe(VersionStatus::Active);
});

it('returns null when no active version exists', function (): void {
    $repo = new FakeDefinitionVersionRepository;
    $handler = new GetActiveDefinitionVersionHandler($repo);

    $result = $handler->handle(new GetActiveDefinitionVersionQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toBeNull();
});
