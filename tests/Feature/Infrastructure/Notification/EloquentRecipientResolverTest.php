<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Team\TeamMemberModel;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Infrastructure\Notification\EloquentRecipientResolver;
use Illuminate\Support\Facades\Hash;

function createResolverUsers(): void
{
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c001', 'name' => 'User 1', 'email' => 'resolver1@test.com', 'password' => Hash::make('password')]);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c002', 'name' => 'User 2', 'email' => 'resolver2@test.com', 'password' => Hash::make('password')]);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c003', 'name' => 'User 3', 'email' => 'resolver3@test.com', 'password' => Hash::make('password')]);
}

it('resolves team members', function (): void {
    createResolverUsers();

    TeamModel::create(['id' => '00000000-0000-0000-0000-00000000c001', 'name' => 'Team A', 'slug' => 'team-a-resolver', 'description' => '']);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-00000000c001', 'user_id' => '550e8400-e29b-41d4-a716-44665544c001', 'joined_at' => now()]);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-00000000c001', 'user_id' => '550e8400-e29b-41d4-a716-44665544c002', 'joined_at' => now()]);

    $resolver = new EloquentRecipientResolver;

    $members = $resolver->resolveTeamMembers('00000000-0000-0000-0000-00000000c001');

    expect($members)->toHaveCount(2)
        ->and($members)->toContain('550e8400-e29b-41d4-a716-44665544c001')
        ->and($members)->toContain('550e8400-e29b-41d4-a716-44665544c002');
});

it('returns empty list for team with no members', function (): void {
    TeamModel::create(['id' => '00000000-0000-0000-0000-00000000c002', 'name' => 'Empty Team', 'slug' => 'empty-team-resolver', 'description' => '']);

    $resolver = new EloquentRecipientResolver;

    expect($resolver->resolveTeamMembers('00000000-0000-0000-0000-00000000c002'))->toBe([]);
});

it('resolves team with subteam members recursively', function (): void {
    createResolverUsers();

    TeamModel::create(['id' => '00000000-0000-0000-0000-00000000c010', 'name' => 'Parent', 'slug' => 'parent-resolver', 'description' => '', 'parent_team_id' => null]);
    TeamModel::create(['id' => '00000000-0000-0000-0000-00000000c011', 'name' => 'Child', 'slug' => 'child-resolver', 'description' => '', 'parent_team_id' => '00000000-0000-0000-0000-00000000c010']);

    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-00000000c010', 'user_id' => '550e8400-e29b-41d4-a716-44665544c001', 'joined_at' => now()]);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-00000000c011', 'user_id' => '550e8400-e29b-41d4-a716-44665544c002', 'joined_at' => now()]);

    $resolver = new EloquentRecipientResolver;

    $members = $resolver->resolveTeamWithSubteamMembers('00000000-0000-0000-0000-00000000c010');

    expect($members)->toHaveCount(2)
        ->and($members)->toContain('550e8400-e29b-41d4-a716-44665544c001')
        ->and($members)->toContain('550e8400-e29b-41d4-a716-44665544c002');
});

it('deduplicates users in multiple subteams', function (): void {
    createResolverUsers();

    TeamModel::create(['id' => '00000000-0000-0000-0000-00000000c020', 'name' => 'Root', 'slug' => 'root-dedup', 'description' => '', 'parent_team_id' => null]);
    TeamModel::create(['id' => '00000000-0000-0000-0000-00000000c021', 'name' => 'Sub1', 'slug' => 'sub1-dedup', 'description' => '', 'parent_team_id' => '00000000-0000-0000-0000-00000000c020']);

    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-00000000c020', 'user_id' => '550e8400-e29b-41d4-a716-44665544c001', 'joined_at' => now()]);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-00000000c021', 'user_id' => '550e8400-e29b-41d4-a716-44665544c001', 'joined_at' => now()]);

    $resolver = new EloquentRecipientResolver;

    $members = $resolver->resolveTeamWithSubteamMembers('00000000-0000-0000-0000-00000000c020');

    expect($members)->toHaveCount(1)
        ->and($members[0])->toBe('550e8400-e29b-41d4-a716-44665544c001');
});
