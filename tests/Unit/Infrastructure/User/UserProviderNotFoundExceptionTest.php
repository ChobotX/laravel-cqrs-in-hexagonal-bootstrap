<?php

declare(strict_types=1);

use App\Infrastructure\User\UserProviderNotFoundException;

it('contains the driver name in the message', function (): void {
    $exception = new UserProviderNotFoundException('users');

    expect($exception->getMessage())->toBe("User provider 'users' could not be created.");
});
