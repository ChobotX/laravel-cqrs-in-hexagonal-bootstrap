<?php

declare(strict_types=1);

use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use App\Domain\Label\Query\SearchLabels\SearchLabelsHandler;
use App\Domain\Label\Query\SearchLabels\SearchLabelsQuery;
use Tests\Helper\FakeLabelRepository;

it('delegates search to the repository', function (): void {
    $label = new Label(
        new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('users'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository(['550e8400-e29b-41d4-a716-446655440000' => $label]);

    $handler = new SearchLabelsHandler($repository);

    $result = $handler->handle(new SearchLabelsQuery(
        namespace: 'users',
        term: 'imp',
        excludeIds: [],
        limit: 50,
    ));

    expect($result)->toHaveCount(1)
        ->and($result[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('filters by namespace', function (): void {
    $label1 = new Label(
        new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('users'),
        new LabelName('important'),
    );
    $label2 = new Label(
        new LabelId('660e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('teams'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $label1,
        '660e8400-e29b-41d4-a716-446655440000' => $label2,
    ]);

    $handler = new SearchLabelsHandler($repository);

    $result = $handler->handle(new SearchLabelsQuery(
        namespace: 'users',
        term: 'imp',
    ));

    expect($result)->toHaveCount(1)
        ->and($result[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('excludes specified label IDs', function (): void {
    $label = new Label(
        new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('users'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository(['550e8400-e29b-41d4-a716-446655440000' => $label]);

    $handler = new SearchLabelsHandler($repository);

    $result = $handler->handle(new SearchLabelsQuery(
        namespace: 'users',
        term: 'imp',
        excludeIds: ['550e8400-e29b-41d4-a716-446655440000'],
    ));

    expect($result)->toBe([]);
});
