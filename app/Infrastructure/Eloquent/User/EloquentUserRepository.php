<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;
use Illuminate\Database\Query\Builder;

final readonly class EloquentUserRepository implements UserRepository
{
    public function __construct(
        private UserMapper $userMapper,
    ) {}

    /** @return list<User> */
    public function all(): array
    {
        return array_values(
            UserModel::all()
                ->map(fn (UserModel $userModel): User => $this->userMapper->toDomain($userModel))
                ->all(),
        );
    }

    public function findById(UserId $userId): ?User
    {
        $model = UserModel::find($userId->value);

        if ($model === null) {
            return null;
        }

        return $this->userMapper->toDomain($model);
    }

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', $email)->first();

        if (! $model instanceof UserModel) {
            return null;
        }

        return $this->userMapper->toDomain($model);
    }

    public function create(User $user): void
    {
        $userModel = new UserModel;
        $userModel->id = $user->id->value;
        $userModel->name = $user->name;
        $userModel->email = $user->email->value;
        $userModel->save();
    }

    public function update(User $user): void
    {
        $userModel = UserModel::findOrFail($user->id->value);
        $userModel->name = $user->name;
        $userModel->email = $user->email->value;
        $userModel->save();
    }

    public function delete(UserId $userId): void
    {
        $model = UserModel::find($userId->value);

        if ($model instanceof UserModel) {
            $model->tokens()->delete();
            $model->delete();
        }
    }

    /** @return list<User> */
    public function search(string $term, array $restrictToOrganizationIds, array $excludeUserIds, int $limit): array
    {
        $builder = UserModel::query()
            ->where(function ($q) use ($term): void {
                $q->whereRaw('unaccent(name) ILIKE unaccent(?)', [sprintf('%%%s%%', $term)])
                    ->orWhereRaw('unaccent(email) ILIKE unaccent(?)', [sprintf('%%%s%%', $term)]);
            });

        if ($restrictToOrganizationIds !== []) {
            $builder->whereIn('id', function (Builder $builder) use ($restrictToOrganizationIds): void {
                $builder->select('user_id')
                    ->from('organization_members')
                    ->whereIn('organization_id', $restrictToOrganizationIds);
            });
        }

        if ($excludeUserIds !== []) {
            $builder->whereNotIn('id', $excludeUserIds);
        }

        return array_values(
            $builder->limit($limit)
                ->get()
                ->map(fn (UserModel $userModel): User => $this->userMapper->toDomain($userModel))
                ->all(),
        );
    }
}
