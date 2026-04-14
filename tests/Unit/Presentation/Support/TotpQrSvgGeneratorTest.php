<?php

declare(strict_types=1);

use App\Presentation\Support\TotpQrSvgGenerator;

it('writes an svg document for an otpauth uri', function (): void {
    $otpauthUri = 'otpauth://totp/Example%20Issuer:user%40example.com?secret=JBSWY3DPEHPK3PXP&issuer=Example%20Issuer';

    $svg = (new TotpQrSvgGenerator)->fromOtpauthUri($otpauthUri);

    expect($svg)->toContain('<svg')
        ->and($svg)->toContain('</svg>');
});
