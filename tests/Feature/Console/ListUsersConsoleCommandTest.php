<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Artisan;

it('lists all users', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440520',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440521',
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
    ]);

    Artisan::call('user:list');
    $output = Artisan::output();

    expect($output)->toContain('John Doe')
        ->toContain('jane@example.com');
});

it('shows message when no users exist', function (): void {
    $this->artisan('user:list')
        ->expectsOutputToContain('No users found.')
        ->assertSuccessful();
});
