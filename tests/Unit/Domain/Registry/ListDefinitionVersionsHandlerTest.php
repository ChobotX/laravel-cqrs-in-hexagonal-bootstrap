<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\Query\ListDefinitionVersionsQuery;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\Handler\Query\ListDefinitionVersionsHandler;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\ValueObject\VersionNumber;
use Tests\Helper\FakeDefinitionVersionRepository;

it('returns versions for a definition', function (): void {
    $schema = new Schema([new StringField('name', 'Name', true)]);
    $definitionId = new DefinitionId('550e8400-e29b-41d4-a716-446655440000');

    $v1 = new DefinitionVersion(
        new DefinitionVersionId('660e8400-e29b-41d4-a716-446655440000'),
        $definitionId,
        new VersionNumber(1),
        $schema,
        VersionStatus::Deprecated,
    );

    $v2 = new DefinitionVersion(
        new DefinitionVersionId('770e8400-e29b-41d4-a716-446655440000'),
        $definitionId,
        new VersionNumber(2),
        $schema,
        VersionStatus::Active,
    );

    $repo = new FakeDefinitionVersionRepository([
        '660e8400-e29b-41d4-a716-446655440000' => $v1,
        '770e8400-e29b-41d4-a716-446655440000' => $v2,
    ]);
    $handler = new ListDefinitionVersionsHandler($repo);

    $result = $handler->handle(new ListDefinitionVersionsQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toHaveCount(2);
});

it('returns empty list when no versions exist', function (): void {
    $repo = new FakeDefinitionVersionRepository;
    $handler = new ListDefinitionVersionsHandler($repo);

    $result = $handler->handle(new ListDefinitionVersionsQuery(definitionId: '550e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toHaveCount(0);
});
