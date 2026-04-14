<?php

declare(strict_types=1);

use App\Infrastructure\User\LaravelTwoFactorCodeNotifier;
use Illuminate\Support\Facades\Mail;

it('sends email for two-factor code', function (): void {
    Mail::shouldReceive('raw')->once();

    $notifier = new LaravelTwoFactorCodeNotifier;
    $notifier->send('user@example.com', '2FA subject', '2FA body');
});
