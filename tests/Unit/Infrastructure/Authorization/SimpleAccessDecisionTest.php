<?php

declare(strict_types=1);

use App\Infrastructure\Authorization\SimpleAccessDecision;

it('returns granted true when constructed as granted', function (): void {
    $decision = new SimpleAccessDecision(granted: true, scope: 'all');

    expect($decision->granted())->toBeTrue();
});

it('returns granted false when constructed as denied', function (): void {
    $decision = new SimpleAccessDecision(granted: false, scope: 'own');

    expect($decision->granted())->toBeFalse();
});

it('returns scope value', function (): void {
    $decision = new SimpleAccessDecision(granted: true, scope: 'team');

    expect($decision->scope())->toBe('team');
});
