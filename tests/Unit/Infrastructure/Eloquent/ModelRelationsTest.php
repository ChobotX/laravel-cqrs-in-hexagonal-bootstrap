<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\RolePermissionModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use App\Infrastructure\Eloquent\Team\TeamMemberModel;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

uses(Tests\TestCase::class);

it('RolePermissionModel belongs to role', function (): void {
    $model = new RolePermissionModel;
    expect($model->role())->toBeInstanceOf(BelongsTo::class);
});

it('UserRoleModel belongs to role', function (): void {
    $model = new UserRoleModel;
    expect($model->role())->toBeInstanceOf(BelongsTo::class);
});

it('RoleModel has many permissions', function (): void {
    $model = new RoleModel;
    expect($model->permissions())->toBeInstanceOf(HasMany::class);
});

it('RoleModel has userRoles relation', function (): void {
    $model = new RoleModel;
    expect($model->userRoles())->toBeInstanceOf(HasMany::class);
});

it('RoleModel has factory', function (): void {
    $factory = RoleModel::factory();
    expect($factory)->toBeInstanceOf(Database\Factories\RoleModelFactory::class);
});

it('TeamModel has parent and children relations', function (): void {
    $model = new TeamModel;
    expect($model->parent())->toBeInstanceOf(BelongsTo::class)
        ->and($model->children())->toBeInstanceOf(HasMany::class)
        ->and($model->members())->toBeInstanceOf(HasMany::class);
});

it('TeamMemberModel belongs to team and user', function (): void {
    $model = new TeamMemberModel;
    expect($model->team())->toBeInstanceOf(BelongsTo::class)
        ->and($model->user())->toBeInstanceOf(BelongsTo::class);
});

it('TenantModel has many domains', function (): void {
    $model = new TenantModel;
    expect($model->domains())->toBeInstanceOf(HasMany::class);
});

it('TenantModel casts database_password as encrypted', function (): void {
    $model = new TenantModel;
    $casts = new ReflectionMethod($model, 'casts')->invoke($model);
    expect($casts)->toHaveKey('database_password', 'encrypted');
});
