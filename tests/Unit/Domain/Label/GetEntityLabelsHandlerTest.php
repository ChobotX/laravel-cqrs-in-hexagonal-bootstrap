<?php

declare(strict_types=1);

use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\Contract\Query\GetEntityLabels\GetEntityLabelsQuery;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use App\Domain\Label\Query\GetEntityLabels\GetEntityLabelsHandler;
use Tests\Helper\FakeLabelRepository;

it('delegates to the repository and returns labels for the entity', function (): void {
    $label = new Label(
        new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('users'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository(
        labels: ['550e8400-e29b-41d4-a716-446655440000' => $label],
        assignments: [['labelId' => '550e8400-e29b-41d4-a716-446655440000', 'labelableId' => '770e8400-e29b-41d4-a716-446655440000']],
    );

    $handler = new GetEntityLabelsHandler($repository);

    $result = $handler->handle(new GetEntityLabelsQuery(
        labelableId: '770e8400-e29b-41d4-a716-446655440000',
    ));

    expect($result)->toHaveCount(1)
        ->and($result[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('returns empty array when entity has no labels', function (): void {
    $repository = new FakeLabelRepository;

    $handler = new GetEntityLabelsHandler($repository);

    $result = $handler->handle(new GetEntityLabelsQuery(
        labelableId: '770e8400-e29b-41d4-a716-446655440000',
    ));

    expect($result)->toBe([]);
});
