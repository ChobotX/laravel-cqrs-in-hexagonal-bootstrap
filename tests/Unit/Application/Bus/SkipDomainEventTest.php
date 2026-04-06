<?php

declare(strict_types=1);

use App\Application\Bus\SkipDomainEvent;

it('stores reason', function (): void {
    $attribute = new SkipDomainEvent('Infrastructure provisioning — no domain state change');
    expect($attribute->reason)->toBe('Infrastructure provisioning — no domain state change');
});
