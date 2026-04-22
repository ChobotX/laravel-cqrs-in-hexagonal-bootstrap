<?php

declare(strict_types=1);

use App\Contract\Attribute\SkipPermissionCheck;

it('stores reason', function (): void {
    $attribute = new SkipPermissionCheck('System bootstrap');
    expect($attribute->reason)->toBe('System bootstrap');
});
