<?php

declare(strict_types=1);

use App\Infrastructure\Sso\LaravelSsoLoginSession;
use Illuminate\Session\Store;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

it('writes and pulls a user id through the session', function (): void {
    $store = new Store('test', new NullSessionHandler);
    $session = new LaravelSsoLoginSession($store);

    $session->setLastResolvedUserId('user-1');

    expect($session->pullLastResolvedUserId())->toBe('user-1')
        ->and($session->pullLastResolvedUserId())->toBeNull();
});

it('returns null when the stored value is not a string', function (): void {
    $store = new Store('test', new NullSessionHandler);
    $store->put('sso.last_resolved_user_id', ['not', 'a', 'string']);

    $session = new LaravelSsoLoginSession($store);

    expect($session->pullLastResolvedUserId())->toBeNull();
});
