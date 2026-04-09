<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\ValueObject\EmailTemplateLocale;
use App\Domain\EmailTemplate\Exception\InvalidEmailTemplateLocaleException;

it('creates valid two-letter locale', function (): void {
    $locale = new EmailTemplateLocale('en');

    expect($locale->value)->toBe('en')
        ->and((string) $locale)->toBe('en');
});

it('creates valid cs locale', function (): void {
    $locale = new EmailTemplateLocale('cs');

    expect($locale->value)->toBe('cs');
});

it('throws for three-letter string', function (): void {
    new EmailTemplateLocale('eng');
})->throws(InvalidEmailTemplateLocaleException::class);

it('throws for uppercase string', function (): void {
    new EmailTemplateLocale('EN');
})->throws(InvalidEmailTemplateLocaleException::class);

it('throws for empty string', function (): void {
    new EmailTemplateLocale('');
})->throws(InvalidEmailTemplateLocaleException::class);

it('throws for string starting with digit', function (): void {
    new EmailTemplateLocale('1a');
})->throws(InvalidEmailTemplateLocaleException::class);

it('compares equality', function (): void {
    $a = new EmailTemplateLocale('en');
    $b = new EmailTemplateLocale('en');
    $c = new EmailTemplateLocale('cs');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
