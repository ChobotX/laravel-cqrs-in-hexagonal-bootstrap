<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Command\LinkSsoIdentityCommand;
use App\Domain\Sso\Contract\Event\SsoIdentityLinked;
use App\Domain\Sso\Handler\Command\LinkSsoIdentityHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserSsoIdentityRepository;
use Tests\Helper\SsoFixtures;

it('persists a new identity link and emits an event', function (): void {
    $repository = new FakeUserSsoIdentityRepository;
    $events = new FakeEventCollector;
    $handler = new LinkSsoIdentityHandler($repository, $events);

    $handler->handle(new LinkSsoIdentityCommand(
        id: SsoFixtures::IDENTITY_ID,
        userId: SsoFixtures::USER_ID,
        configurationId: SsoFixtures::CONFIG_ID,
        subject: 'subject-1',
        emailAtLink: 'user@example.com',
    ));

    expect($repository->created)->toHaveCount(1)
        ->and($repository->created[0]->subject)->toBe('subject-1')
        ->and($events->collected[0])->toBeInstanceOf(SsoIdentityLinked::class);
});
