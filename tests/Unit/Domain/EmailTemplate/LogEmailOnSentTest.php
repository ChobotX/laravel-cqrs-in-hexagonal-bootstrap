<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent;
use App\Domain\EmailTemplate\EventHandler\LogEmailOnSent;
use Tests\Helper\FakeEmailLogRepository;

it('creates email log from TemplatedEmailSent event', function (): void {
    $repository = new FakeEmailLogRepository;
    $handler = new LogEmailOnSent($repository);

    $event = new TemplatedEmailSent(
        emailLogId: '550e8400-e29b-41d4-a716-446655440000',
        templateType: 'user_invite',
        locale: 'en',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        recipientEmail: 'john@example.com',
        renderedSubject: 'You have been invited',
        renderedBodyMasked: '<h2>Welcome!</h2><p>Hello John, link: ***</p>',
        variableKeys: ['userName', 'link', 'tenantName'],
        traceId: 'trace-abc-123',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    );

    $handler->handle($event);

    expect($repository->created)->toHaveCount(1);

    $log = $repository->created[0];
    expect($log->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($log->templateType)->toBe('user_invite')
        ->and($log->locale)->toBe('en')
        ->and($log->recipientId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($log->recipientEmail)->toBe('john@example.com')
        ->and($log->renderedSubject)->toBe('You have been invited')
        ->and($log->renderedBodyMasked)->toBe('<h2>Welcome!</h2><p>Hello John, link: ***</p>')
        ->and($log->variableKeys)->toBe(['userName', 'link', 'tenantName'])
        ->and($log->traceId)->toBe('trace-abc-123')
        ->and($log->sentAt)->toEqual(new DateTimeImmutable('2026-01-15T10:00:00+00:00'));
});

it('handles null trace id', function (): void {
    $repository = new FakeEmailLogRepository;
    $handler = new LogEmailOnSent($repository);

    $event = new TemplatedEmailSent(
        emailLogId: '550e8400-e29b-41d4-a716-446655440000',
        templateType: 'password_reset',
        locale: 'cs',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        recipientEmail: 'jane@example.com',
        renderedSubject: 'Password Reset',
        renderedBodyMasked: '<p>Reset link: ***</p>',
        variableKeys: ['link', 'tenantName'],
        traceId: null,
        occurredAt: new DateTimeImmutable('2026-02-01T12:00:00+00:00'),
    );

    $handler->handle($event);

    expect($repository->created)->toHaveCount(1)
        ->and($repository->created[0]->traceId)->toBeNull();
});
