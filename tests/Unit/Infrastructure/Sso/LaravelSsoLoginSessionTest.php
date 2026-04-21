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

it('returns null when the stored user id is not a string', function (): void {
    $store = new Store('test', new NullSessionHandler);
    $store->put('sso.last_resolved_user_id', ['not', 'a', 'string']);

    $session = new LaravelSsoLoginSession($store);

    expect($session->pullLastResolvedUserId())->toBeNull();
});

it('stores and consumes a handshake when state matches', function (): void {
    $store = new Store('test', new NullSessionHandler);
    $session = new LaravelSsoLoginSession($store);

    $session->rememberHandshake('primary', 'state-1', 'nonce-1');

    expect($session->consumeHandshake('primary', 'state-1'))->toBe('nonce-1')
        ->and($session->consumeHandshake('primary', 'state-1'))->toBeNull();
});

it('returns null when state does not match', function (): void {
    $store = new Store('test', new NullSessionHandler);
    $session = new LaravelSsoLoginSession($store);

    $session->rememberHandshake('primary', 'state-1');

    expect($session->consumeHandshake('primary', 'state-other'))->toBeNull();
});

it('returns null when no handshake stored for slug', function (): void {
    $store = new Store('test', new NullSessionHandler);

    expect(new LaravelSsoLoginSession($store)->consumeHandshake('missing', 'state'))->toBeNull();
});

it('clear removes all session entries', function (): void {
    $store = new Store('test', new NullSessionHandler);
    $session = new LaravelSsoLoginSession($store);

    $session->setLastResolvedUserId('user-1');
    $session->rememberHandshake('primary', 'state', 'nonce');

    $session->clear();

    expect($session->pullLastResolvedUserId())->toBeNull()
        ->and($session->consumeHandshake('primary', 'state'))->toBeNull();
});
