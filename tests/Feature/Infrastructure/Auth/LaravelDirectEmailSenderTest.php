<?php

declare(strict_types=1);

use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Infrastructure\Auth\LaravelDirectEmailSender;
use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\Eloquent\User\UserMapper;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Hash;

function emailSenderRepo(): EloquentUserRepository
{
    return new EloquentUserRepository(new UserMapper);
}

it('sends email to existing user', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440760',
        'name' => 'Email User',
        'email' => 'email-user@example.com',
        'password' => Hash::make('password123'),
    ]);

    $sent = false;
    $mock = Mockery::mock(Mailer::class);
    $mock->shouldReceive('raw')
        ->once()
        ->withArgs(function (string $body, Closure $callback) use (&$sent): bool {
            expect($body)->toBe('Test body content');
            $sent = true;

            return true;
        });

    $sender = new LaravelDirectEmailSender(
        emailSenderRepo(),
        $mock,
    );

    $sender->sendToUser(
        '550e8400-e29b-41d4-a716-446655440760',
        'Test Subject',
        'Test body content',
    );

    expect($sent)->toBeTrue();
});

it('throws UserNotFoundException for non-existent user', function (): void {
    $sender = new LaravelDirectEmailSender(
        emailSenderRepo(),
        app(Mailer::class),
    );

    $sender->sendToUser(
        '550e8400-e29b-41d4-a716-446655440761',
        'Test Subject',
        'Test body content',
    );
})->throws(UserNotFoundException::class);

it('sends raw email with correct subject and recipient', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440762',
        'name' => 'Verify Email',
        'email' => 'verify-email@example.com',
        'password' => Hash::make('password123'),
    ]);

    $capturedEmail = null;
    $capturedSubject = null;

    $mock = Mockery::mock(Mailer::class);
    $mock->shouldReceive('raw')
        ->once()
        ->withArgs(function (string $body, Closure $callback): bool {
            expect($body)->toBe('Hello body');
            $mock = Mockery::mock(Message::class);
            $mock->shouldReceive('to')
                ->once()
                ->with('verify-email@example.com');
            $mock->shouldReceive('subject')
                ->once()
                ->with('Hello Subject');
            $callback($mock);

            return true;
        });

    $sender = new LaravelDirectEmailSender(
        emailSenderRepo(),
        $mock,
    );

    $sender->sendToUser(
        '550e8400-e29b-41d4-a716-446655440762',
        'Hello Subject',
        'Hello body',
    );
});
