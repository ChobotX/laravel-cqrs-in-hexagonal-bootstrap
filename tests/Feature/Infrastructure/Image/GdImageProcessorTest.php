<?php

declare(strict_types=1);

use App\Infrastructure\Image\GdImageProcessor;

it('resizes and converts to webp', function (): void {
    $source = imagecreatetruecolor(1000, 800);
    $tempPath = tempnam(sys_get_temp_dir(), 'test_img_').'.png';
    imagepng($source, $tempPath);
    imagedestroy($source);

    $processor = new GdImageProcessor;
    $result = $processor->resizeAndConvertToWebp(new SplFileInfo($tempPath), 640);

    expect($result->getPathname())->toEndWith('.webp')
        ->and($result->getSize())->toBeGreaterThan(0);

    $resultImage = imagecreatefromwebp($result->getPathname());
    expect(imagesx($resultImage))->toBe(640)
        ->and(imagesy($resultImage))->toBe(512);
    imagedestroy($resultImage);

    unlink($tempPath);
    unlink($result->getPathname());
});

it('preserves small images without upscaling', function (): void {
    $source = imagecreatetruecolor(100, 100);
    $tempPath = tempnam(sys_get_temp_dir(), 'test_img_').'.png';
    imagepng($source, $tempPath);
    imagedestroy($source);

    $processor = new GdImageProcessor;
    $result = $processor->resizeAndConvertToWebp(new SplFileInfo($tempPath), 640);

    $resultImage = imagecreatefromwebp($result->getPathname());
    expect(imagesx($resultImage))->toBe(100)
        ->and(imagesy($resultImage))->toBe(100);
    imagedestroy($resultImage);

    unlink($tempPath);
    unlink($result->getPathname());
});

it('handles gif input', function (): void {
    $source = imagecreatetruecolor(100, 100);
    $tempPath = tempnam(sys_get_temp_dir(), 'test_img_').'.gif';
    imagegif($source, $tempPath);
    imagedestroy($source);

    $processor = new GdImageProcessor;
    $result = $processor->resizeAndConvertToWebp(new SplFileInfo($tempPath), 640);

    expect($result->getPathname())->toEndWith('.webp');

    unlink($tempPath);
    unlink($result->getPathname());
});

it('handles webp input', function (): void {
    $source = imagecreatetruecolor(100, 100);
    $tempPath = tempnam(sys_get_temp_dir(), 'test_img_').'.webp';
    imagewebp($source, $tempPath);
    imagedestroy($source);

    $processor = new GdImageProcessor;
    $result = $processor->resizeAndConvertToWebp(new SplFileInfo($tempPath), 640);

    expect($result->getPathname())->toEndWith('.webp');

    unlink($tempPath);
    unlink($result->getPathname());
});

it('throws on corrupt image', function (): void {
    $tempPath = tempnam(sys_get_temp_dir(), 'test_corrupt_').'.jpg';
    file_put_contents($tempPath, 'not an image');

    $processor = new GdImageProcessor;

    try {
        $processor->resizeAndConvertToWebp(new SplFileInfo($tempPath), 640);
    } finally {
        unlink($tempPath);
    }
})->throws(App\Domain\File\Exception\ImageProcessingException::class);

it('handles portrait images', function (): void {
    $source = imagecreatetruecolor(400, 1000);
    $tempPath = tempnam(sys_get_temp_dir(), 'test_img_').'.jpg';
    imagejpeg($source, $tempPath);
    imagedestroy($source);

    $processor = new GdImageProcessor;
    $result = $processor->resizeAndConvertToWebp(new SplFileInfo($tempPath), 640);

    $resultImage = imagecreatefromwebp($result->getPathname());
    expect(imagesx($resultImage))->toBe(256)
        ->and(imagesy($resultImage))->toBe(640);
    imagedestroy($resultImage);

    unlink($tempPath);
    unlink($result->getPathname());
});
