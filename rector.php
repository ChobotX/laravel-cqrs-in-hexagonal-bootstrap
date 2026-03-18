<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/tests',
    ])
    ->withPreparedSets(
        codeQuality: true,
        deadCode: true,
        earlyReturn: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        codingStyle: true,
        rectorPreset: true,
    )
    ->withSets([
        LevelSetList::UP_TO_PHP_84,
    ])
    ->withSkip([
        PostIncDecToPreIncDecRector::class,
    ]);
