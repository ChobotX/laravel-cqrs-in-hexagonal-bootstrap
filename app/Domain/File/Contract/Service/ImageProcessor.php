<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Service;

use SplFileInfo;

/**
 * Domain service contract for image in the File bounded context.
 */
interface ImageProcessor
{
    /** Contract operation `resizeAndConvertToWebp`; see infrastructure for behavior. */
    public function resizeAndConvertToWebp(SplFileInfo $source, int $maxDimension): SplFileInfo;
}
