<?php

declare(strict_types=1);

use App\Application\Event\EntityUpdated;
use App\Application\Event\PropertyChange;
use App\Domain\Sso\Contract\Event\SsoConfigurationCreated;
use App\Domain\Sso\Contract\Event\SsoConfigurationDeleted;
use App\Domain\Sso\Contract\Event\SsoConfigurationUpdated;
use App\Domain\Sso\Contract\Event\SsoIdentityLinked;
use App\Domain\Sso\Contract\Event\SsoIdentityUnlinked;
use App\Domain\Sso\Contract\Event\SsoLoginFailed;
use App\Domain\Sso\Contract\Event\SsoLoginSucceeded;

$now = new DateTimeImmutable('2026-01-01T00:00:00+00:00');

it('exposes SsoConfigurationCreated metadata', function () use ($now): void {
    $event = new SsoConfigurationCreated('cfg-1', 'oidc', 'primary', 'Primary', true, $now);

    expect($event->entityId())->toBe('cfg-1')
        ->and($event->entityType())->toBe('sso_configuration')
        ->and($event->occurredAt())->toBe($now)
        ->and($event->actionLabel())->toBe('Sso Configuration Created');
});

it('exposes SsoConfigurationUpdated change set', function () use ($now): void {
    $change = new PropertyChange('display_name', 'Old', 'New');
    $event = new SsoConfigurationUpdated('cfg-1', [$change], $now);

    expect($event)->toBeInstanceOf(EntityUpdated::class)
        ->and($event->changes())->toBe([$change])
        ->and($event->entityId())->toBe('cfg-1')
        ->and($event->entityType())->toBe('sso_configuration')
        ->and($event->occurredAt())->toBe($now);
});

it('exposes SsoConfigurationDeleted metadata', function () use ($now): void {
    $event = new SsoConfigurationDeleted('cfg-1', $now);

    expect($event->entityId())->toBe('cfg-1')
        ->and($event->entityType())->toBe('sso_configuration')
        ->and($event->occurredAt())->toBe($now);
});

it('exposes SsoLoginSucceeded metadata', function () use ($now): void {
    $event = new SsoLoginSucceeded('cfg-1', 'user-1', 'subject', 'user@example.com', true, $now);

    expect($event->entityId())->toBe('user-1')
        ->and($event->entityType())->toBe('sso_login')
        ->and($event->occurredAt())->toBe($now);
});

it('exposes SsoLoginFailed metadata', function () use ($now): void {
    $event = new SsoLoginFailed('cfg-1', 'reason', 'user@example.com', 'subject', $now);

    expect($event->entityId())->toBe('cfg-1')
        ->and($event->entityType())->toBe('sso_login')
        ->and($event->occurredAt())->toBe($now);
});

it('exposes SsoIdentityLinked metadata', function () use ($now): void {
    $event = new SsoIdentityLinked('id-1', 'user-1', 'cfg-1', 'subject', $now);

    expect($event->entityId())->toBe('id-1')
        ->and($event->entityType())->toBe('sso_identity')
        ->and($event->occurredAt())->toBe($now);
});

it('exposes SsoIdentityUnlinked metadata', function () use ($now): void {
    $event = new SsoIdentityUnlinked('id-1', 'user-1', 'cfg-1', $now);

    expect($event->entityId())->toBe('id-1')
        ->and($event->entityType())->toBe('sso_identity')
        ->and($event->occurredAt())->toBe($now);
});
