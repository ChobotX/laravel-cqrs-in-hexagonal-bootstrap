<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use App\Domain\Organization\OrganizationMember;
use DateTimeImmutable;

final readonly class OrganizationMemberMapper
{
    public function toDomain(OrganizationMemberModel $organizationMemberModel): OrganizationMember
    {
        $user = $organizationMemberModel->user;

        return new OrganizationMember(
            userId: $organizationMemberModel->user_id,
            organizationId: $organizationMemberModel->organization_id,
            userName: $user !== null ? $user->name : '',
            userEmail: $user !== null ? $user->email : '',
            joinedAt: new DateTimeImmutable($organizationMemberModel->joined_at),
        );
    }
}
