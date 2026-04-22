<?php

declare(strict_types=1);

use App\Contract\Attribute\SkipTransaction;

it('stores reason', function (): void {
    $attribute = new SkipTransaction('Writes to landlord connection');
    expect($attribute->reason)->toBe('Writes to landlord connection');
});
