<?php

declare(strict_types=1);

use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\Exception\LabelAlreadyExistsException;
use App\Domain\Label\Label;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Exception\TeamSlugAlreadyExistsException;
use App\Domain\Team\Team;
use App\Domain\Team\TeamName;
use App\Domain\Team\TeamSlug;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Email;
use App\Domain\User\Exception\EmailAlreadyExistsException;
use App\Domain\User\User;
use App\Domain\User\UserName;
use App\Infrastructure\Eloquent\Authorization\EloquentRoleRepository;
use App\Infrastructure\Eloquent\Authorization\RoleMapper;
use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Label\EloquentLabelRepository;
use App\Infrastructure\Eloquent\Label\LabelMapper;
use App\Infrastructure\Eloquent\Team\EloquentTeamRepository;
use App\Infrastructure\Eloquent\Team\TeamMapper;
use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\Eloquent\User\UserMapper;

it('throws EmailAlreadyExistsException on duplicate email during create', function (): void {
    $repo = new EloquentUserRepository(new UserMapper);

    $user1 = new User(new UserId('550e8400-e29b-41d4-a716-446655440c01'), new UserName('Alice'), new Email('dupe@test.com'));
    $user2 = new User(new UserId('550e8400-e29b-41d4-a716-446655440c02'), new UserName('Bob'), new Email('dupe@test.com'));

    $repo->create($user1);
    $repo->create($user2);
})->throws(EmailAlreadyExistsException::class);

it('throws EmailAlreadyExistsException on duplicate email during update', function (): void {
    $repo = new EloquentUserRepository(new UserMapper);

    $user1 = new User(new UserId('550e8400-e29b-41d4-a716-446655440c03'), new UserName('Alice'), new Email('alice-u@test.com'));
    $user2 = new User(new UserId('550e8400-e29b-41d4-a716-446655440c04'), new UserName('Bob'), new Email('bob-u@test.com'));

    $repo->create($user1);
    $repo->create($user2);

    $user2Updated = new User(new UserId('550e8400-e29b-41d4-a716-446655440c04'), new UserName('Bob'), new Email('alice-u@test.com'));
    $repo->update($user2Updated);
})->throws(EmailAlreadyExistsException::class);

it('throws TeamSlugAlreadyExistsException on duplicate slug', function (): void {
    $repo = new EloquentTeamRepository(new TeamMapper);

    $team1 = new Team(new TeamId('550e8400-e29b-41d4-a716-446655440c05'), new TeamName('Team A'), new TeamSlug('dupe-slug'), 'Desc', null);
    $team2 = new Team(new TeamId('550e8400-e29b-41d4-a716-446655440c06'), new TeamName('Team B'), new TeamSlug('dupe-slug'), 'Desc', null);

    $repo->create($team1);
    $repo->create($team2);
})->throws(TeamSlugAlreadyExistsException::class);

it('throws RoleAlreadyExistsException on duplicate role name', function (): void {
    $repo = new EloquentRoleRepository(new RoleMapper);

    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c07', 'name' => 'DupeRole', 'description' => 'D', 'is_system' => false]);

    $role = $repo->findById(new App\Domain\Authorization\Contract\RoleId('550e8400-e29b-41d4-a716-446655440c07'));
    assert($role instanceof App\Domain\Authorization\Contract\Role);

    $dupe = new App\Domain\Authorization\Contract\Role(
        new App\Domain\Authorization\Contract\RoleId('550e8400-e29b-41d4-a716-446655440c08'),
        new App\Domain\Authorization\RoleName('DupeRole'),
        'Another',
        false,
        [],
    );

    $repo->create($dupe);
})->throws(App\Domain\Authorization\Exception\RoleAlreadyExistsException::class);

it('throws LabelAlreadyExistsException on duplicate namespace+name', function (): void {
    $repo = new EloquentLabelRepository(new LabelMapper);

    $label1 = new Label(new LabelId('550e8400-e29b-41d4-a716-446655440c09'), new LabelNamespace('test'), new LabelName('dupe'));
    $label2 = new Label(new LabelId('550e8400-e29b-41d4-a716-446655440c0a'), new LabelNamespace('test'), new LabelName('dupe'));

    $repo->create($label1);
    $repo->create($label2);
})->throws(LabelAlreadyExistsException::class);
