<?php

declare(strict_types=1);

use App\Presentation\Support\StackGapClassMap;

it('resolves stack gap tokens', function (): void {
    expect(StackGapClassMap::forGap('loose'))->toBe('gap-12')
        ->and(StackGapClassMap::forGap('default'))->toBe('gap-8')
        ->and(StackGapClassMap::forGap('unknown-token'))->toBe('gap-8');
});

it('matches resources/shared/ui-stack-gaps.json used by Vue', function (): void {
    $path = resource_path('shared/ui-stack-gaps.json');
    $raw = file_get_contents($path);
    if ($raw === false) {
        throw new RuntimeException('Cannot read '.$path);
    }

    /** @var array{stackGap: array<string, string>} $data */
    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

    foreach ($data['stackGap'] as $token => $class) {
        expect(StackGapClassMap::forGap($token))->toBe($class);
    }
});
