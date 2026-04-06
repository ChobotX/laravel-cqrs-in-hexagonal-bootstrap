<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

it('shows accept invite form for non-activated user', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440700',
        'name' => 'Invited User',
        'email' => 'invited@example.com',
        'password' => null,
    ]);

    $url = URL::temporarySignedRoute('invite.accept', now()->addHours(1), [
        'userId' => '550e8400-e29b-41d4-a716-446655440700',
    ]);

    $this->get($url)
        ->assertOk()
        ->assertViewIs('auth.accept-invite')
        ->assertSee('Invited User');
});

it('redirects to login when user is already activated', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440701',
        'name' => 'Activated User',
        'email' => 'activated@example.com',
        'password' => Hash::make('password123'),
    ]);

    $url = URL::temporarySignedRoute('invite.accept', now()->addHours(1), [
        'userId' => '550e8400-e29b-41d4-a716-446655440701',
    ]);

    $this->get($url)
        ->assertRedirect('/login')
        ->assertSessionHas('error');
});

it('rejects unsigned invite show request', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440702',
        'name' => 'Unsigned User',
        'email' => 'unsigned@example.com',
        'password' => null,
    ]);

    $this->get('/invite/550e8400-e29b-41d4-a716-446655440702')
        ->assertForbidden();
});

it('accepts invite and logs in user', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440703',
        'name' => 'Accept User',
        'email' => 'accept@example.com',
        'password' => null,
    ]);

    $url = URL::temporarySignedRoute('invite.accept.store', now()->addHours(1), [
        'userId' => '550e8400-e29b-41d4-a716-446655440703',
    ]);

    $this->post($url, [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/users')
        ->assertSessionHas('success');

    $this->assertAuthenticated();
    $this->assertAuthenticatedAs(UserModel::find('550e8400-e29b-41d4-a716-446655440703'));

    $updated = UserModel::find('550e8400-e29b-41d4-a716-446655440703');
    expect($updated->password)->not->toBeNull();
});

it('validates password is required for invite acceptance', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440704',
        'name' => 'Validation User',
        'email' => 'validation@example.com',
        'password' => null,
    ]);

    $url = URL::temporarySignedRoute('invite.accept.store', now()->addHours(1), [
        'userId' => '550e8400-e29b-41d4-a716-446655440704',
    ]);

    $this->post($url, [])
        ->assertSessionHasErrors(['password']);
});

it('validates password minimum length for invite acceptance', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440705',
        'name' => 'Short Pass User',
        'email' => 'shortpass@example.com',
        'password' => null,
    ]);

    $url = URL::temporarySignedRoute('invite.accept.store', now()->addHours(1), [
        'userId' => '550e8400-e29b-41d4-a716-446655440705',
    ]);

    $this->post($url, [
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors(['password']);
});

it('validates password confirmation for invite acceptance', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440706',
        'name' => 'Mismatch User',
        'email' => 'mismatch@example.com',
        'password' => null,
    ]);

    $url = URL::temporarySignedRoute('invite.accept.store', now()->addHours(1), [
        'userId' => '550e8400-e29b-41d4-a716-446655440706',
    ]);

    $this->post($url, [
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ])->assertSessionHasErrors(['password']);
});

it('rejects unsigned invite post request', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440707',
        'name' => 'Unsigned Post User',
        'email' => 'unsigned-post@example.com',
        'password' => null,
    ]);

    $this->post('/invite/550e8400-e29b-41d4-a716-446655440707', [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertForbidden();
});

it('redirects authenticated user away from invite page', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440708',
        'name' => 'Authed User',
        'email' => 'authed-invite@example.com',
        'password' => Hash::make('password123'),
    ]);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440709',
        'name' => 'Target Invite',
        'email' => 'target-invite@example.com',
        'password' => null,
    ]);

    $url = URL::temporarySignedRoute('invite.accept', now()->addHours(1), [
        'userId' => '550e8400-e29b-41d4-a716-446655440709',
    ]);

    $this->actingAs($user)
        ->get($url)
        ->assertRedirect();
});
