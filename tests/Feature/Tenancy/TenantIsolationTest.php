<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

it('isolates users between tenant schemas', function (): void {
    UserModel::create([
        'id' => Str::uuid()->toString(),
        'name' => 'Tenant A User',
        'email' => 'user-a@test.com',
        'password' => Hash::make('password'),
    ]);

    $countA = DB::connection('tenant')->table('users')->count();

    /** @var object{cnt: int} $row */
    $row = DB::connection('tenant')
        ->selectOne('SELECT count(*) as cnt FROM tenant_test_b.users');

    expect($countA)->toBe(1)
        ->and($row->cnt)->toBe(0);
});

it('allows same-named roles in different schemas', function (): void {
    RoleModel::create([
        'id' => Str::uuid()->toString(),
        'name' => 'Editor',
        'description' => 'Tenant A editor',
        'is_system' => false,
    ]);

    DB::connection('tenant')->statement(
        "INSERT INTO tenant_test_b.roles (id, name, description, is_system, created_at, updated_at) VALUES (?, 'Editor', 'Tenant B editor', false, NOW(), NOW())",
        [Str::uuid()->toString()],
    );

    $countA = DB::connection('tenant')->table('roles')->where('name', 'Editor')->count();

    /** @var object{cnt: int} $row */
    $row = DB::connection('tenant')
        ->selectOne("SELECT count(*) as cnt FROM tenant_test_b.roles WHERE name = 'Editor'");

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
        ->selectOne("SELECT count(*) as cnt FROM tenant_test_b.users WHERE email = 'secret@test.com'");

    expect($row->cnt)->toBe(0);
});
