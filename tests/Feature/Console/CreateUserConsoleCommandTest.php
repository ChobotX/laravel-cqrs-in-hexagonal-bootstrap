<?php

declare(strict_types=1);

it('creates a user', function (): void {
    $this->artisan('user:create', ['name' => 'John Doe', 'email' => 'john@example.com'])
        ->expectsOutputToContain('User created with ID:')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});
