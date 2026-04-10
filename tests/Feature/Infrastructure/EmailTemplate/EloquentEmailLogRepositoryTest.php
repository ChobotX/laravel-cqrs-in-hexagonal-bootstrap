<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Entity\EmailLog;
use App\Domain\EmailTemplate\Contract\ValueObject\EmailLogId;
use App\Infrastructure\Eloquent\EmailTemplate\EloquentEmailLogRepository;
use App\Infrastructure\Eloquent\EmailTemplate\EmailLogMapper;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function makeEloquentLogRepository(): EloquentEmailLogRepository
{
    return new EloquentEmailLogRepository(new EmailLogMapper);
}

function makeEloquentEmailLog(
    string $id,
    string $recipientId,
    string $templateType = 'user_invite',
    string $locale = 'en',
    string $sentAt = '2026-01-15 10:00:00',
): EmailLog {
    return new EmailLog(
        new EmailLogId($id),
        $templateType,
        $locale,
        $recipientId,
        'recipient@example.com',
        'Rendered subject',
        '<p>Masked body</p>',
        ['userName', 'link'],
        'trace-abc-123',
        new DateTimeImmutable($sentAt),
    );
}

function seedUser(string $id, string $email): UserModel
{
    return UserModel::create([
        'id' => $id,
        'name' => 'Log Test User',
        'email' => $email,
        'password' => Hash::make('password123'),
    ]);
}

// ─── create ─────────────────────────────────────────────────────────────────

it('persists an email log entry', function (): void {
    $userModel = seedUser('550e8400-e29b-41d4-a716-000000000001', 'log-test-1@example.com');
    $eloquentEmailLogRepository = makeEloquentLogRepository();
    $emailLog = makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b01', $userModel->id);

    $eloquentEmailLogRepository->create($emailLog);

    $this->assertDatabaseHas('email_logs', [
        'id' => '550e8400-e29b-41d4-a716-446655440b01',
        'template_type' => 'user_invite',
        'locale' => 'en',
        'recipient_id' => $userModel->id,
        'recipient_email' => 'recipient@example.com',
        'rendered_subject' => 'Rendered subject',
        'rendered_body_masked' => '<p>Masked body</p>',
        'trace_id' => 'trace-abc-123',
    ]);
});

it('persists email log with null trace id', function (): void {
    $userModel = seedUser('550e8400-e29b-41d4-a716-000000000002', 'log-test-2@example.com');
    $eloquentEmailLogRepository = makeEloquentLogRepository();
    $log = new EmailLog(
        new EmailLogId('550e8400-e29b-41d4-a716-446655440b02'),
        'user_invite',
        'en',
        $userModel->id,
        'notrace@example.com',
        'Subject',
        '<p>Body</p>',
        [],
        null,
        new DateTimeImmutable('2026-01-15 10:00:00'),
    );

    $eloquentEmailLogRepository->create($log);

    $this->assertDatabaseHas('email_logs', [
        'id' => '550e8400-e29b-41d4-a716-446655440b02',
        'trace_id' => null,
    ]);
});

// ─── findByRecipient ────────────────────────────────────────────────────────

it('finds logs for a specific recipient ordered by sent_at descending', function (): void {
    $userModel = seedUser('550e8400-e29b-41d4-a716-000000000010', 'log-test-10@example.com');
    $otherUser = seedUser('550e8400-e29b-41d4-a716-999999999999', 'log-other@example.com');
    $eloquentEmailLogRepository = makeEloquentLogRepository();

    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b10', $userModel->id, sentAt: '2026-01-10 10:00:00'));
    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b11', $userModel->id, sentAt: '2026-01-20 10:00:00'));
    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b12', $otherUser->id, sentAt: '2026-01-15 10:00:00'));

    $results = $eloquentEmailLogRepository->findByRecipient($userModel->id, 10, 0);

    expect($results)->toHaveCount(2);
    expect($results[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440b11'); // newer first
    expect($results[1]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440b10');
});

it('respects limit and offset when finding by recipient', function (): void {
    $userModel = seedUser('550e8400-e29b-41d4-a716-000000000020', 'log-test-20@example.com');
    $eloquentEmailLogRepository = makeEloquentLogRepository();

    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b20', $userModel->id, sentAt: '2026-01-01 00:00:00'));
    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b21', $userModel->id, sentAt: '2026-01-02 00:00:00'));
    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b22', $userModel->id, sentAt: '2026-01-03 00:00:00'));

    $firstPage = $eloquentEmailLogRepository->findByRecipient($userModel->id, 2, 0);
    $secondPage = $eloquentEmailLogRepository->findByRecipient($userModel->id, 2, 2);

    expect($firstPage)->toHaveCount(2)
        ->and($secondPage)->toHaveCount(1);
});

it('returns empty list when no logs found for recipient', function (): void {
    $eloquentEmailLogRepository = makeEloquentLogRepository();

    $results = $eloquentEmailLogRepository->findByRecipient('550e8400-e29b-41d4-a716-000000000099', 10, 0);

    expect($results)->toBe([]);
});

// ─── findAll ────────────────────────────────────────────────────────────────

it('returns all logs ordered by sent_at descending', function (): void {
    $userModel = seedUser('550e8400-e29b-41d4-a716-000000000030', 'log-test-30@example.com');
    $userB = seedUser('550e8400-e29b-41d4-a716-000000000031', 'log-test-31@example.com');
    $eloquentEmailLogRepository = makeEloquentLogRepository();

    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b30', $userModel->id, sentAt: '2026-02-01 00:00:00'));
    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b31', $userB->id, sentAt: '2026-02-03 00:00:00'));

    $results = $eloquentEmailLogRepository->findAll(10, 0);

    expect($results)->not->toBeEmpty();
    expect($results[0])->toBeInstanceOf(EmailLog::class);
});

it('respects limit and offset in findAll', function (): void {
    $userModel = seedUser('550e8400-e29b-41d4-a716-000000000040', 'log-test-40@example.com');
    $eloquentEmailLogRepository = makeEloquentLogRepository();

    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b40', $userModel->id, sentAt: '2026-03-01 00:00:00'));
    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b41', $userModel->id, sentAt: '2026-03-02 00:00:00'));
    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b42', $userModel->id, sentAt: '2026-03-03 00:00:00'));

    $page1 = $eloquentEmailLogRepository->findAll(2, 0);
    $page2 = $eloquentEmailLogRepository->findAll(2, 2);

    expect($page1)->toHaveCount(2)
        ->and($page2)->toHaveCount(1);
});

// ─── countAll ───────────────────────────────────────────────────────────────

it('counts all email logs', function (): void {
    $userModel = seedUser('550e8400-e29b-41d4-a716-000000000050', 'log-test-50@example.com');
    $eloquentEmailLogRepository = makeEloquentLogRepository();

    $countBefore = $eloquentEmailLogRepository->countAll();
    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b50', $userModel->id));
    $eloquentEmailLogRepository->create(makeEloquentEmailLog('550e8400-e29b-41d4-a716-446655440b51', $userModel->id));

    expect($eloquentEmailLogRepository->countAll())->toBe($countBefore + 2);
});
