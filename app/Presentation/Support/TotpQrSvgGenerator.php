<?php

declare(strict_types=1);

namespace App\Presentation\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

final readonly class TotpQrSvgGenerator
{
    private const int QR_PIXEL_SIZE = 220;

    public function fromOtpauthUri(string $otpauthUri): string
    {
        $imageRenderer = new ImageRenderer(
            new RendererStyle(self::QR_PIXEL_SIZE),
            new SvgImageBackEnd,
        );

        return new Writer($imageRenderer)->writeString($otpauthUri);
    }
}
