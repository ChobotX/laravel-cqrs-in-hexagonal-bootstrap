<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Infrastructure\Organization\EloquentOrganizationMembershipChecker;
use Illuminate\Support\Facades\Hash;

it('returns true when user is a member', function (): void {
    $user = UserModel::create(['id' => '00000000-0000-0000-0000-000000000010', 'name' => 'Test', 'email' => 'mc1@test.com', 'password' => Hash::make('p')]);
    OrganizationModel::create(['id' => '550e8400-e29b-41d4-a716-446655440000', 'name' => 'Org', 'slug' => 'mc-org-1', 'description' => '']);

    OrganizationMemberModel::create([
        'user_id' => $user->id,
        'organization_id' => '550e8400-e29b-41d4-a716-446655440000',
        'joined_at' => now(),
    ]);

    $checker = new EloquentOrganizationMembershipChecker;

    expect($checker->isMember($user->id, '550e8400-e29b-41d4-a716-446655440000'))->toBeTrue();
});

it('returns false when user is not a member', function (): void {
    $checker = new EloquentOrganizationMembershipChecker;

    expect($checker->isMember('00000000-0000-0000-0000-000000000010', '550e8400-e29b-41d4-a716-446655440000'))->toBeFalse();
});

it('returns member organization ids', function (): void {
    $user = UserModel::create(['id' => '00000000-0000-0000-0000-000000000020', 'name' => 'Test', 'email' => 'mc2@test.com', 'password' => Hash::make('p')]);
    OrganizationModel::create(['id' => '550e8400-e29b-41d4-a716-446655440001', 'name' => 'Org A', 'slug' => 'mc-org-2', 'description' => '']);
    OrganizationModel::create(['id' => '550e8400-e29b-41d4-a716-446655440002', 'name' => 'Org B', 'slug' => 'mc-org-3', 'description' => '']);

    OrganizationMemberModel::create([
        'user_id' => $user->id,
        'organization_id' => '550e8400-e29b-41d4-a716-446655440001',
        'joined_at' => now(),
    ]);

    OrganizationMemberModel::create([
        'user_id' => $user->id,
        'organization_id' => '550e8400-e29b-41d4-a716-446655440002',
        'joined_at' => now(),
    ]);

    $checker = new EloquentOrganizationMembershipChecker;

    $ids = $checker->memberOrganizationIds($user->id);

    expect($ids)->toHaveCount(2)
        ->and($ids)->toContain('550e8400-e29b-41d4-a716-446655440001', '550e8400-e29b-41d4-a716-446655440002');
});
