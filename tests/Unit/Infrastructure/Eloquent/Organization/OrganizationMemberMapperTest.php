<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberMapper;
use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\User\UserModel;

it('maps a member model to domain', function (): void {
    $userModel = new UserModel;
    $userModel->id = '00000000-0000-0000-0000-000000000010';
    $userModel->name = 'John Doe';
    $userModel->email = 'john@test.com';

    $model = new OrganizationMemberModel;
    $model->user_id = '00000000-0000-0000-0000-000000000010';
    $model->organization_id = '550e8400-e29b-41d4-a716-446655440000';
    $model->joined_at = '2025-01-15 10:00:00';
    $model->setRelation('user', $userModel);

    $mapper = new OrganizationMemberMapper;
    $organizationMember = $mapper->toDomain($model);

    expect($organizationMember->userId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($organizationMember->organizationId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($organizationMember->userName)->toBe('John Doe')
        ->and($organizationMember->userEmail)->toBe('john@test.com')
        ->and($organizationMember->joinedAt->format('Y-m-d H:i:s'))->toBe('2025-01-15 10:00:00');
});
