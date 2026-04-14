<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

it('shows reset second-factor section for super admin on user edit', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440701',
        'name' => 'Super',
        'email' => 'super-2fa-reset@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $victim = UserModel::factory()->create([
        'name' => 'Victim User',
        'email' => 'victim-2fa@example.com',
    ]);

    $this->actingAs($admin)
        ->get('/users/'.$victim->id.'/edit')
        ->assertStatus(200)
        ->assertSee(__('messages.users.two_factor_reset_section_title'), false);
});

it('resets two-factor for target user when super admin confirms', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440702',
        'name' => 'Super',
        'email' => 'super-2fa-reset-post@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $victim = UserModel::factory()->create([
        'name' => 'Victim Two',
        'email' => 'victim-2fa-post@example.com',
        'email_two_factor_enabled' => true,
        'email_two_factor_confirmed_at' => now(),
        'totp_secret' => 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ',
        'totp_confirmed_at' => now(),
        'totp_recovery_code_hashes' => ['hash-a'],
    ]);

    DB::connection('tenant')->table('email_two_factor_challenges')->insert([
        'id' => '00000000-0000-0000-0000-000000000701',
        'user_id' => $victim->id,
        'code_hash' => 'x',
        'expires_at' => now()->addMinutes(5),
        'consumed_at' => null,
        'attempts' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->post('/users/'.$victim->id.'/reset-two-factor')
        ->assertRedirect()
        ->assertSessionHas('success', __('messages.users.two_factor_reset'));

    $victim->refresh();

    expect($victim->email_two_factor_enabled)->toBeFalse()
        ->and($victim->email_two_factor_confirmed_at)->toBeNull()
        ->and($victim->totp_secret)->toBeNull()
        ->and($victim->totp_confirmed_at)->toBeNull()
        ->and($victim->totp_recovery_code_hashes)->toBeNull();

    expect(DB::connection('tenant')->table('email_two_factor_challenges')->where('user_id', $victim->id)->count())->toBe(0);
});

it('forbids reset two-factor without user_recovery permission', function (): void {
    $this->seedSuperAdminRole();
    $managerRole = $this->seedRoleWithPermissions('Manager for 2FA gate', 'Full users module', [
        'users' => 'all',
    ]);

    $manager = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440703',
        'name' => 'Manager',
        'email' => 'manager-2fa-reset@example.com',
        'password' => Hash::make('password'),
    ]);
    $this->assignRole($manager->id, $managerRole->id);

    $victim = UserModel::factory()->create([
        'name' => 'Victim Three',
        'email' => 'victim-2fa-forbidden@example.com',
        'totp_secret' => 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ',
        'totp_confirmed_at' => now(),
    ]);

    $this->actingAs($manager)
        ->post('/users/'.$victim->id.'/reset-two-factor')
        ->assertForbidden();
});
