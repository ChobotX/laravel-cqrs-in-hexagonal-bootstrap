<?php

declare(strict_types=1);

namespace App\Infrastructure\File;

use App\Domain\File\Contract\Service\ImageProcessor;
use App\Domain\File\Exception\ImageProcessingException;
use ErrorException;
use GdImage;
use SplFileInfo;

final readonly class GdImageProcessor implements ImageProcessor
{
    private const int WEBP_QUALITY = 80;

    private const string MIME_PNG = 'image/png';

    private const string MIME_GIF = 'image/gif';

    private const string MIME_WEBP = 'image/webp';

    public function resizeAndConvertToWebp(SplFileInfo $source, int $maxDimension): SplFileInfo
    {
        $mimeType = mime_content_type($source->getPathname());
        $gdImage = $this->createFromFile($source->getPathname(), $mimeType !== false ? $mimeType : '');

        $originalWidth = imagesx($gdImage);
        $originalHeight = imagesy($gdImage);

        [$newWidth, $newHeight] = $this->calculateDimensions($originalWidth, $originalHeight, $maxDimension);

        /** @var GdImage $resized */
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $gdImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        imagedestroy($gdImage);

        $tempPath = tempnam(sys_get_temp_dir(), 'img_').'.webp';
        imagewebp($resized, $tempPath, self::WEBP_QUALITY);
        imagedestroy($resized);

        return new SplFileInfo($tempPath);
    }

    private function createFromFile(string $path, string $mimeType): GdImage
    {
        try {
            /** @var GdImage $image */
            $image = match ($mimeType) {
                self::MIME_PNG => imagecreatefrompng($path),
                self::MIME_GIF => imagecreatefromgif($path),
                self::MIME_WEBP => imagecreatefromwebp($path),
                default => imagecreatefromjpeg($path),
            };
        } catch (ErrorException) {
            throw new ImageProcessingException(sprintf('Failed to load image from %s.', $path));
        }

        return $image;
    }

    /** @return array{0: int<1, max>, 1: int<1, max>} */
    private function calculateDimensions(int $width, int $height, int $maxDimension): array
    {
        if ($width <= $maxDimension && $height <= $maxDimension) {
            return [max(1, $width), max(1, $height)];
        }

        if ($width >= $height) {
            return [max(1, $maxDimension), max(1, (int) round($height * $maxDimension / $width))];
        }

        return [max(1, (int) round($width * $maxDimension / $height)), max(1, $maxDimension)];
    }
}
