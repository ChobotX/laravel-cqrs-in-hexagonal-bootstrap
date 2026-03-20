<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use App\Domain\Organization\OrganizationMember;
use App\Domain\Organization\OrganizationMemberRepository;
use App\Domain\Organization\TeamMemberRepository;
use App\Infrastructure\Eloquent\Authorization\UserPermissionOverrideModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

final readonly class EloquentOrganizationMemberRepository implements OrganizationMemberRepository
{
    public function __construct(
        private OrganizationMemberMapper $organizationMemberMapper,
        private TeamMemberRepository $teamMemberRepository,
    ) {}

    public function add(string $userId, string $organizationId): void
    {
        $organizationMemberModel = new OrganizationMemberModel;
        $organizationMemberModel->user_id = $userId;
        $organizationMemberModel->organization_id = $organizationId;
        $organizationMemberModel->joined_at = (new DateTimeImmutable)->format('Y-m-d H:i:s');
        $organizationMemberModel->save();
    }

    public function remove(string $userId, string $organizationId): void
    {
        DB::transaction(function () use ($userId, $organizationId): void {
            $this->teamMemberRepository->removeAllByUserAndOrganization($userId, $organizationId);

            UserRoleModel::where('user_id', $userId)
                ->where('organization_id', $organizationId)
                ->delete();

            UserPermissionOverrideModel::where('user_id', $userId)
                ->where('organization_id', $organizationId)
                ->delete();

            OrganizationMemberModel::where('user_id', $userId)
                ->where('organization_id', $organizationId)
                ->delete();
        });
    }

    public function isMember(string $userId, string $organizationId): bool
    {
        return OrganizationMemberModel::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->exists();
    }

    /** @return list<string> */
    public function memberOrganizationIds(string $userId): array
    {
        /** @var list<string> $ids */
        $ids = array_values(
            OrganizationMemberModel::where('user_id', $userId)
                ->pluck('organization_id')
                ->all(),
        );

        return $ids;
    }

    /** @return list<OrganizationMember> */
    public function listMembers(string $organizationId): array
    {
        $models = OrganizationMemberModel::with('user')
            ->where('organization_id', $organizationId)
            ->get();

        return array_values(
            $models->map(fn (OrganizationMemberModel $organizationMemberModel): OrganizationMember => $this->organizationMemberMapper->toDomain($organizationMemberModel))->all(),
        );
    }
}
