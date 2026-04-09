<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\EmailTemplate\Handler\Command\SendTemplatedEmailHandler;
use Tests\Helper\FakeTemplatedEmailDispatcher;

it('delegates to TemplatedEmailDispatcher with correct arguments', function (): void {
    $dispatcher = new FakeTemplatedEmailDispatcher;
    $handler = new SendTemplatedEmailHandler($dispatcher);

    $handler->handle(new SendTemplatedEmailCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        templateType: 'user_invite',
        locale: 'en',
        variables: ['userName' => 'John', 'link' => 'https://example.com/invite/abc'],
    ));

    expect($dispatcher->dispatched)->toHaveCount(1)
        ->and($dispatcher->dispatched[0]['userId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($dispatcher->dispatched[0]['templateType'])->toBe('user_invite')
        ->and($dispatcher->dispatched[0]['locale'])->toBe('en')
        ->and($dispatcher->dispatched[0]['variables'])->toBe(['userName' => 'John', 'link' => 'https://example.com/invite/abc']);
});

it('passes empty variables array when none provided', function (): void {
    $dispatcher = new FakeTemplatedEmailDispatcher;
    $handler = new SendTemplatedEmailHandler($dispatcher);

    $handler->handle(new SendTemplatedEmailCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        templateType: 'password_reset',
        locale: 'cs',
        variables: [],
    ));

    expect($dispatcher->dispatched)->toHaveCount(1)
        ->and($dispatcher->dispatched[0]['variables'])->toBe([]);
});
