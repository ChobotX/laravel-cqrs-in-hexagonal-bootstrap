<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function secondarySchema(): string
{
    $token = getenv('TEST_TOKEN');

    return 'tenant_test_b_'.($token !== false ? $token : '1');
}

it('isolates users between tenant schemas', function (): void {
    UserModel::create([
        'id' => '22222222-2222-2222-2222-222222222201',
        'name' => 'Tenant A User',
        'email' => 'user-a@test.com',
        'password' => Hash::make('password'),
    ]);

    $countA = DB::connection('tenant')->table('users')->count();

    /** @var object{cnt: int} $row */
    $row = DB::connection('tenant')
        ->selectOne(sprintf('SELECT count(*) as cnt FROM %s.users', secondarySchema()));

    expect($countA)->toBe(1)
        ->and($row->cnt)->toBe(0);
});

it('allows same-named roles in different schemas', function (): void {
    RoleModel::create([
        'id' => '33333333-3333-3333-3333-333333333301',
        'name' => 'Editor',
        'description' => 'Tenant A editor',
        'is_system' => false,
    ]);

    DB::connection('tenant')->statement(
        sprintf("INSERT INTO %s.roles (id, name, description, is_system, created_at, updated_at) VALUES (?, 'Editor', 'Tenant B editor', false, NOW(), NOW())", secondarySchema()),
        ['33333333-3333-3333-3333-333333333302'],
    );

    $countA = DB::connection('tenant')->table('roles')->where('name', 'Editor')->count();

    /** @var object{cnt: int} $row */
    $row = DB::connection('tenant')
        ->selectOne(sprintf("SELECT count(*) as cnt FROM %s.roles WHERE name = 'Editor'", secondarySchema()));

    expect($countA)->toBe(1)
        ->and($row->cnt)->toBe(1);
});

it('does not leak user data across schemas', function (): void {
    UserModel::create([
        'id' => '11111111-1111-1111-1111-111111111111',
        'name' => 'Secret User',
        'email' => 'secret@test.com',
        'password' => Hash::make('password'),
    ]);

    /** @var object{cnt: int} $row */
    $row = DB::connection('tenant')
        ->selectOne(sprintf("SELECT count(*) as cnt FROM %s.users WHERE email = 'secret@test.com'", secondarySchema()));

    expect($row->cnt)->toBe(0);
});
