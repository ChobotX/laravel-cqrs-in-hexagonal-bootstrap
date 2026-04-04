<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Service;

use SplFileInfo;

interface ImageProcessor
{
    public function resizeAndConvertToWebp(SplFileInfo $source, int $maxDimension): SplFileInfo;
}
