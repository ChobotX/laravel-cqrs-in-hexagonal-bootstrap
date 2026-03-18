<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Artisan;

it('displays user in table', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440510',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    Artisan::call('user:get', ['id' => '550e8400-e29b-41d4-a716-446655440510']);
    $output = Artisan::output();

    expect($output)->toContain('John Doe')
        ->toContain('john@example.com');
});

it('fails with error when user not found', function (): void {
    $this->artisan('user:get', ['id' => '550e8400-e29b-41d4-a716-446655440511'])
        ->expectsOutputToContain('User with id [550e8400-e29b-41d4-a716-446655440511] not found.')
        ->assertFailed();
});
