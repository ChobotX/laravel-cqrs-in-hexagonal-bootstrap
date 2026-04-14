<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Contract\IdGenerator;
use App\Domain\User\Contract\Repository\PasswordHistoryRepository;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

final readonly class EloquentPasswordHistoryRepository implements PasswordHistoryRepository
{
    public function __construct(
        private IdGenerator $idGenerator,
    ) {}

    public function listRecentHashes(string $userId, int $limit): array
    {
        /** @var list<string> $hashes */
        $hashes = DB::connection('tenant')->table('user_password_history')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->pluck('password_hash')
            ->all();

        return $hashes;
    }

    public function append(string $userId, string $passwordHash, DateTimeImmutable $createdAt): void
    {
        DB::connection('tenant')->table('user_password_history')->insert([
            'id' => $this->idGenerator->generate(),
            'user_id' => $userId,
            'password_hash' => $passwordHash,
            'created_at' => $createdAt,
        ]);
    }

    public function pruneToMaxEntries(string $userId, int $keepCount): void
    {
        /** @var list<string> $keepIds */
        $keepIds = DB::connection('tenant')->table('user_password_history')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($keepCount)
            ->pluck('id')
            ->all();

        if ($keepIds === []) {
            return;
        }

        DB::connection('tenant')->table('user_password_history')
            ->where('user_id', $userId)
            ->whereNotIn('id', $keepIds)
            ->delete();
    }
}
