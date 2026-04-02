<?php

declare(strict_types=1);

namespace App\Contract\Image;

use SplFileInfo;

interface ImageProcessor
{
    public function resizeAndConvertToWebp(SplFileInfo $source, int $maxDimension): SplFileInfo;
}
