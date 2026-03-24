<?php

declare(strict_types=1);

use App\Contract\Team\TeamMembershipChecker;
use App\Infrastructure\Eloquent\Team\EloquentTeamMemberRepository;
use App\Infrastructure\Eloquent\Team\TeamMemberMapper;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;

/** @return array{UserModel, UserModel, UserModel, EloquentTeamMemberRepository} */
function scopeTestSetup(): array
{
    // Team hierarchy: Managers -> Engineering, Managers -> Design
    TeamModel::create([
        'id' => '00000000-0000-0000-0000-100000000001',
        'name' => 'Managers',
        'slug' => 'managers',
        'description' => 'Management team',
    ]);

    TeamModel::create([
        'id' => '00000000-0000-0000-0000-100000000002',
        'parent_team_id' => '00000000-0000-0000-0000-100000000001',
        'name' => 'Engineering',
        'slug' => 'engineering',
        'description' => 'Engineering sub-team',
    ]);

    TeamModel::create([
        'id' => '00000000-0000-0000-0000-100000000003',
        'parent_team_id' => '00000000-0000-0000-0000-100000000001',
        'name' => 'Design',
        'slug' => 'design',
        'description' => 'Design sub-team',
    ]);

    $manager = UserModel::factory()->create();
    $engineer = UserModel::factory()->create();
    $designer = UserModel::factory()->create();

    $repo = new EloquentTeamMemberRepository(new TeamMemberMapper);

    // Manager is in the parent team
    $repo->add($manager->id, '00000000-0000-0000-0000-100000000001');
    // Engineer is in Engineering sub-team only
    $repo->add($engineer->id, '00000000-0000-0000-0000-100000000002');
    // Designer is in Design sub-team only
    $repo->add($designer->id, '00000000-0000-0000-0000-100000000003');

    return [$manager, $engineer, $designer, $repo];
}

it('manager sees data from all descendant teams', function (): void {
    [$manager, $engineer, $designer, $repo] = scopeTestSetup();

    $teamIds = $repo->memberTeamIds($manager->id);

    // Manager's direct team (Managers) + both children (Engineering, Design)
    expect($teamIds)->toHaveCount(3)
        ->and($teamIds)->toContain(
            '00000000-0000-0000-0000-100000000001',
            '00000000-0000-0000-0000-100000000002',
            '00000000-0000-0000-0000-100000000003',
        );
});

it('engineer sees only their own team', function (): void {
    [$manager, $engineer, $designer, $repo] = scopeTestSetup();

    $teamIds = $repo->memberTeamIds($engineer->id);

    expect($teamIds)->toHaveCount(1)
        ->and($teamIds)->toContain('00000000-0000-0000-0000-100000000002');
});

it('designer sees only their own team', function (): void {
    [$manager, $engineer, $designer, $repo] = scopeTestSetup();

    $teamIds = $repo->memberTeamIds($designer->id);

    expect($teamIds)->toHaveCount(1)
        ->and($teamIds)->toContain('00000000-0000-0000-0000-100000000003');
});

it('engineer cannot see manager or design team data', function (): void {
    [$manager, $engineer, $designer, $repo] = scopeTestSetup();

    $teamIds = $repo->memberTeamIds($engineer->id);

    expect($teamIds)->not->toContain('00000000-0000-0000-0000-100000000001')
        ->and($teamIds)->not->toContain('00000000-0000-0000-0000-100000000003');
});

it('TeamMembershipChecker delegates to repository for scope resolution', function (): void {
    [$manager, $engineer] = scopeTestSetup();

    $teamMembershipChecker = app(TeamMembershipChecker::class);

    // Manager sees all 3 teams
    $managerTeamIds = $teamMembershipChecker->memberTeamIds($manager->id);
    expect($managerTeamIds)->toHaveCount(3);

    // Engineer sees only Engineering
    $engineerTeamIds = $teamMembershipChecker->memberTeamIds($engineer->id);
    expect($engineerTeamIds)->toHaveCount(1)
        ->and($engineerTeamIds)->toContain('00000000-0000-0000-0000-100000000002');

    // Direct membership checks
    expect($teamMembershipChecker->isTeamMember($manager->id, '00000000-0000-0000-0000-100000000001'))->toBeTrue()
        ->and($teamMembershipChecker->isTeamMember($engineer->id, '00000000-0000-0000-0000-100000000001'))->toBeFalse()
        ->and($teamMembershipChecker->isTeamMember($engineer->id, '00000000-0000-0000-0000-100000000002'))->toBeTrue();
});

it('directMemberTeamIds returns only direct assignments', function (): void {
    [$manager, $engineer, $designer, $repo] = scopeTestSetup();

    // Manager's direct team is only Managers (not the descendants)
    $directIds = $repo->directMemberTeamIds($manager->id);
    expect($directIds)->toHaveCount(1)
        ->and($directIds)->toContain('00000000-0000-0000-0000-100000000001');

    // memberTeamIds includes descendants
    $allIds = $repo->memberTeamIds($manager->id);
    expect($allIds)->toHaveCount(3);
});
