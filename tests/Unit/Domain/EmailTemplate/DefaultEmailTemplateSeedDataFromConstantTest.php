<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Constant\DefaultEmailTemplates;
use App\Domain\EmailTemplate\Service\DefaultEmailTemplateSeedDataFromConstant;

it('exposes the same default templates as the constant', function (): void {
    $provider = new DefaultEmailTemplateSeedDataFromConstant;

    expect($provider->templatesByTypeLocaleKey())->toBe(DefaultEmailTemplates::DEFAULTS);
});
