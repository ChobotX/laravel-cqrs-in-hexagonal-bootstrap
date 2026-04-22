<?php

declare(strict_types=1);

use App\Domain\User\Contract\ValueObject\UserId;
use App\Infrastructure\User\EloquentEmailTwoFactorChallengeRepository;
use Illuminate\Support\Facades\DB;

it('issues, reads, marks attempt, and consumes email two-factor challenges', function (): void {
    DB::connection('tenant')->table('users')->insert([
        'id' => '550e8400-e29b-41d4-a716-446655440888',
        'name' => 'Challenge User',
        'email' => 'challenge@example.com',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $repository = new EloquentEmailTwoFactorChallengeRepository(new Tests\Helper\FakeIdGenerator);
    $userId = new UserId('550e8400-e29b-41d4-a716-446655440888');
    $repository->issue($userId, 'hash-code', new DateTimeImmutable('+10 minutes'));

    $challenge = $repository->latest($userId);
    expect($challenge)->not->toBeNull();

    $repository->markAttempt($userId);
    $repository->consume($userId);

    $row = DB::connection('tenant')->table('email_two_factor_challenges')->where('user_id', $userId->value)->first();
    expect($row)->toBeInstanceOf(stdClass::class)
        ->and($row->attempts)->toBeNumeric()->toBeGreaterThanOrEqual(1)
        ->and($row->consumed_at)->not->toBeNull();
});

it('handles consume and latest when no challenge exists', function (): void {
    $repository = new EloquentEmailTwoFactorChallengeRepository(new Tests\Helper\FakeIdGenerator);
    $userId = new UserId('550e8400-e29b-41d4-a716-446655440899');

    expect($repository->latest($userId))->toBeNull();

    $repository->consume($userId);

    expect(true)->toBeTrue();
});

it('deletes all challenges for a user', function (): void {
    DB::connection('tenant')->table('users')->insert([
        'id' => '550e8400-e29b-41d4-a716-446655440877',
        'name' => 'Purge User',
        'email' => 'purge-challenges@example.com',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $repository = new EloquentEmailTwoFactorChallengeRepository(new Tests\Helper\FakeIdGenerator);
    $userId = new UserId('550e8400-e29b-41d4-a716-446655440877');
    $repository->issue($userId, 'hash-a', new DateTimeImmutable('+10 minutes'));
    $repository->issue($userId, 'hash-b', new DateTimeImmutable('+10 minutes'));

    $repository->deleteAllForUser($userId);

    expect(DB::connection('tenant')->table('email_two_factor_challenges')->where('user_id', $userId->value)->count())->toBe(0);
});
