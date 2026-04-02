<?php

declare(strict_types=1);

use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\Label;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use App\Domain\Label\Query\GetLabelsForEntities\GetLabelsForEntitiesHandler;
use App\Domain\Label\Query\GetLabelsForEntities\GetLabelsForEntitiesQuery;
use Tests\Helper\FakeLabelRepository;

it('returns labels grouped by entity id', function (): void {
    $labelA = new Label(new LabelId('00000000-0000-0000-0000-00000000000a'), new LabelNamespace('users'), new LabelName('VIP'));

    $repo = new FakeLabelRepository(
        labels: ['00000000-0000-0000-0000-00000000000a' => $labelA],
        assignments: [
            ['labelId' => '00000000-0000-0000-0000-00000000000a', 'labelableId' => 'entity-1'],
        ],
    );

    $handler = new GetLabelsForEntitiesHandler($repo);

    $result = $handler->handle(new GetLabelsForEntitiesQuery(['entity-1', 'entity-2']));

    expect($result)->toHaveCount(2)
        ->and($result['entity-1'])->toHaveCount(1)
        ->and($result['entity-1'][0]->name->value)->toBe('VIP')
        ->and($result['entity-2'])->toBeEmpty();
});

it('returns empty map for empty input', function (): void {
    $repo = new FakeLabelRepository;
    $handler = new GetLabelsForEntitiesHandler($repo);

    $result = $handler->handle(new GetLabelsForEntitiesQuery([]));

    expect($result)->toBeEmpty();
});
