<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\HardDelete;

it('stores reason', function (): void {
    $attr = new HardDelete(reason: 'Test reason');

    expect($attr->reason)->toBe('Test reason');
});
