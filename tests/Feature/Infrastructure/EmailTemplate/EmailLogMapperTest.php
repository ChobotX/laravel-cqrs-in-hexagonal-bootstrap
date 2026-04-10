<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Entity\EmailLog;
use App\Infrastructure\Eloquent\EmailTemplate\EmailLogMapper;
use App\Infrastructure\Eloquent\EmailTemplate\EmailLogModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('maps email log model to domain entity', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-000000000001',
        'name' => 'John Doe',
        'email' => 'john-mapper@example.com',
        'password' => Hash::make('password123'),
    ]);

    EmailLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c01',
        'template_type' => 'user_invite',
        'locale' => 'en',
        'recipient_id' => $user->id,
        'recipient_email' => 'john@example.com',
        'rendered_subject' => 'You have been invited',
        'rendered_body_masked' => '<p>Hello ***</p>',
        'variable_keys' => ['userName', 'link', 'tenantName'],
        'trace_id' => 'trace-abc-123',
        'sent_at' => '2026-01-15 10:00:00',
    ]);

    $model = EmailLogModel::find('550e8400-e29b-41d4-a716-446655440c01');

    $mapper = new EmailLogMapper;
    $emailLog = $mapper->toDomain($model);

    expect($emailLog)->toBeInstanceOf(EmailLog::class)
        ->and($emailLog->id->value)->toBe('550e8400-e29b-41d4-a716-446655440c01')
        ->and($emailLog->templateType)->toBe('user_invite')
        ->and($emailLog->locale)->toBe('en')
        ->and($emailLog->recipientId)->toBe($user->id)
        ->and($emailLog->recipientEmail)->toBe('john@example.com')
        ->and($emailLog->renderedSubject)->toBe('You have been invited')
        ->and($emailLog->renderedBodyMasked)->toBe('<p>Hello ***</p>')
        ->and($emailLog->variableKeys)->toBe(['userName', 'link', 'tenantName'])
        ->and($emailLog->traceId)->toBe('trace-abc-123')
        ->and($emailLog->sentAt)->toBeInstanceOf(DateTimeImmutable::class)
        ->and($emailLog->sentAt->format('Y-m-d'))->toBe('2026-01-15');
});

it('maps email log model with null trace id', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-000000000002',
        'name' => 'Jane Doe',
        'email' => 'jane-mapper@example.com',
        'password' => Hash::make('password123'),
    ]);

    EmailLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c02',
        'template_type' => 'password_reset',
        'locale' => 'cs',
        'recipient_id' => $user->id,
        'recipient_email' => 'jane@example.com',
        'rendered_subject' => 'Obnovení hesla',
        'rendered_body_masked' => '<p>Klikněte zde</p>',
        'variable_keys' => ['link'],
        'trace_id' => null,
        'sent_at' => '2026-02-01 08:30:00',
    ]);

    $model = EmailLogModel::find('550e8400-e29b-41d4-a716-446655440c02');

    $mapper = new EmailLogMapper;
    $emailLog = $mapper->toDomain($model);

    expect($emailLog->traceId)->toBeNull()
        ->and($emailLog->locale)->toBe('cs');
});

it('maps email log model with empty variable keys', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-000000000003',
        'name' => 'Notify User',
        'email' => 'notif-mapper@example.com',
        'password' => Hash::make('password123'),
    ]);

    EmailLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c03',
        'template_type' => 'notification',
        'locale' => 'en',
        'recipient_id' => $user->id,
        'recipient_email' => 'notif@example.com',
        'rendered_subject' => 'Notification',
        'rendered_body_masked' => '<p>Body</p>',
        'variable_keys' => [],
        'trace_id' => 'trace-notif',
        'sent_at' => '2026-03-10 12:00:00',
    ]);

    $model = EmailLogModel::find('550e8400-e29b-41d4-a716-446655440c03');

    $mapper = new EmailLogMapper;
    $emailLog = $mapper->toDomain($model);

    expect($emailLog->variableKeys)->toBe([])
        ->and($emailLog->sentAt->format('Y-m-d'))->toBe('2026-03-10');
});
