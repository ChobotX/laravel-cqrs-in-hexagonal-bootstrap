<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Contract\IdGenerator;
use App\Domain\User\Contract\Repository\EmailTwoFactorChallengeRepository;
use App\Domain\User\Contract\ValueObject\EmailTwoFactorChallenge;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class EloquentEmailTwoFactorChallengeRepository implements EmailTwoFactorChallengeRepository
{
    public function __construct(
        private IdGenerator $idGenerator,
    ) {}

    public function issue(UserId $userId, string $codeHash, DateTimeImmutable $expiresAt): void
    {
        DB::connection('tenant')->table('email_two_factor_challenges')->insert([
            'id' => $this->idGenerator->generate(),
            'user_id' => $userId->value,
            'code_hash' => $codeHash,
            'expires_at' => $expiresAt,
            'consumed_at' => null,
            'attempts' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function latest(UserId $userId): ?EmailTwoFactorChallenge
    {
        $row = DB::connection('tenant')->table('email_two_factor_challenges')
            ->where('user_id', $userId->value)
            ->orderByDesc('created_at')
            ->first();

        if (! $row instanceof stdClass || ! is_string($row->code_hash)) {
            return null;
        }

        $expiresAt = is_string($row->expires_at) ? new DateTimeImmutable($row->expires_at) : new DateTimeImmutable;
        $attempts = is_numeric($row->attempts) ? (int) $row->attempts : 0;

        return new EmailTwoFactorChallenge(
            codeHash: $row->code_hash,
            expiresAt: $expiresAt,
            attempts: $attempts,
            consumed: $row->consumed_at !== null,
        );
    }

    public function markAttempt(UserId $userId): void
    {
        DB::connection('tenant')->table('email_two_factor_challenges')
            ->where('user_id', $userId->value)
            ->orderByDesc('created_at')
            ->limit(1)
            ->increment('attempts');
    }

    public function consume(UserId $userId): void
    {
        $latest = DB::connection('tenant')->table('email_two_factor_challenges')
            ->where('user_id', $userId->value)
            ->orderByDesc('created_at')
            ->first();

        if (! $latest instanceof stdClass || ! is_string($latest->id)) {
            return;
        }

        DB::connection('tenant')->table('email_two_factor_challenges')
            ->where('id', $latest->id)
            ->update([
                'consumed_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function deleteAllForUser(UserId $userId): void
    {
        DB::connection('tenant')->table('email_two_factor_challenges')
            ->where('user_id', $userId->value)
            ->delete();
    }
}
