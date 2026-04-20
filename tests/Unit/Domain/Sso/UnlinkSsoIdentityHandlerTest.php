<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Command\UnlinkSsoIdentityCommand;
use App\Domain\Sso\Contract\Event\SsoIdentityUnlinked;
use App\Domain\Sso\Exception\SsoIdentityNotFoundException;
use App\Domain\Sso\Handler\Command\UnlinkSsoIdentityHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserSsoIdentityRepository;
use Tests\Helper\SsoFixtures;

it('removes the identity row and emits an event', function (): void {
    $identity = SsoFixtures::identity();
    $repository = new FakeUserSsoIdentityRepository([$identity->id->value => $identity]);
    $events = new FakeEventCollector;
    $handler = new UnlinkSsoIdentityHandler($repository, $events);

    $handler->handle(new UnlinkSsoIdentityCommand(id: $identity->id->value));

    expect($repository->deleted)->toBe([$identity->id->value])
        ->and($events->collected[0])->toBeInstanceOf(SsoIdentityUnlinked::class);
});

it('throws when the identity is missing', function (): void {
    $handler = new UnlinkSsoIdentityHandler(new FakeUserSsoIdentityRepository, new FakeEventCollector);

    $handler->handle(new UnlinkSsoIdentityCommand(id: SsoFixtures::IDENTITY_ID));
})->throws(SsoIdentityNotFoundException::class);
