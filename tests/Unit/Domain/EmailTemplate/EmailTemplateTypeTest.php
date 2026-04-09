<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateType;
use App\Domain\EmailTemplate\Exception\InvalidEmailTemplateTypeException;

it('creates valid type with underscore slug', function (): void {
    $type = new EmailTemplateType('user_invite');

    expect($type->value)->toBe('user_invite')
        ->and((string) $type)->toBe('user_invite');
});

it('creates valid type with another slug', function (): void {
    $type = new EmailTemplateType('password_reset');

    expect($type->value)->toBe('password_reset');
});

it('creates valid single-word type', function (): void {
    $type = new EmailTemplateType('notification');

    expect($type->value)->toBe('notification');
});

it('throws for empty string', function (): void {
    new EmailTemplateType('');
})->throws(InvalidEmailTemplateTypeException::class);

it('throws for string with spaces', function (): void {
    new EmailTemplateType('user invite');
})->throws(InvalidEmailTemplateTypeException::class);

it('throws for uppercase string', function (): void {
    new EmailTemplateType('UserInvite');
})->throws(InvalidEmailTemplateTypeException::class);

it('throws for string with special characters', function (): void {
    new EmailTemplateType('user-invite');
})->throws(InvalidEmailTemplateTypeException::class);

it('throws for string starting with number', function (): void {
    new EmailTemplateType('1user');
})->throws(InvalidEmailTemplateTypeException::class);

it('compares equality', function (): void {
    $a = new EmailTemplateType('user_invite');
    $b = new EmailTemplateType('user_invite');
    $c = new EmailTemplateType('password_reset');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
