<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Exception\RecordShareNotFoundException;

it('has correct status code', function (): void {
    $exception = new RecordShareNotFoundException('user-1', 'contact', 'resource-1');

    expect($exception->statusCode())->toBe(404);
});

it('has technical message with details', function (): void {
    $exception = new RecordShareNotFoundException('user-1', 'contact', 'resource-1');

    expect($exception->getMessage())->toBe('Record share for user [user-1] on contact [resource-1] not found.');
});

it('translates user message', function (): void {
    $exception = new RecordShareNotFoundException('user-1', 'contact', 'resource-1');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.record_share_not_found');
});

it('exposes grantee user id, resource type and resource id', function (): void {
    $exception = new RecordShareNotFoundException('user-1', 'contact', 'resource-1');

    expect($exception->granteeUserId)->toBe('user-1')
        ->and($exception->resourceType)->toBe('contact')
        ->and($exception->resourceId)->toBe('resource-1');
});
