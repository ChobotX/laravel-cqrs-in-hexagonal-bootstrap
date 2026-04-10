<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Event\EmailTemplateReset;
use App\Domain\EmailTemplate\Contract\Event\EmailTemplateUpdated;
use App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent;

it('EmailTemplateReset exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-06-15 12:00:00');
    $event = new EmailTemplateReset('user_invite', 'cs', $occurredAt);

    expect($event->occurredAt())->toBe($occurredAt);
});

it('EmailTemplateUpdated exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-06-15 12:00:00');
    $event = new EmailTemplateUpdated('user_invite', 'cs', $occurredAt);

    expect($event->occurredAt())->toBe($occurredAt);
});

it('TemplatedEmailSent exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-06-15 12:00:00');
    $event = new TemplatedEmailSent(
        emailLogId: '550e8400-e29b-41d4-a716-446655440abc',
        templateType: 'user_invite',
        locale: 'cs',
        recipientId: '660e8400-e29b-41d4-a716-446655440abc',
        recipientEmail: 'test@test.com',
        renderedSubject: 'Subject',
        renderedBodyMasked: 'Body',
        variableKeys: ['name'],
        traceId: null,
        occurredAt: $occurredAt,
    );

    expect($event->occurredAt())->toBe($occurredAt);
});
