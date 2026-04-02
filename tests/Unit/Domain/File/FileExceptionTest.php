<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\File\Exception\FileNotFoundException;
use App\Domain\File\Exception\FileStorageException;
use App\Domain\File\Exception\ImageProcessingException;
use App\Domain\File\Exception\InvalidFileIdException;
use App\Domain\File\Exception\InvalidFileNameException;
use App\Domain\File\Exception\InvalidFileNamespaceException;
use App\Domain\File\Exception\InvalidFileVersionException;
use App\Domain\File\Exception\InvalidMimeTypeException;
use App\Domain\File\Exception\InvalidStoragePathException;

function fileTestTranslator(): Translator
{
    return new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key.':'.implode(',', $params);
        }
    };
}

it('InvalidFileIdException has status 422 and translates', function (): void {
    $e = new InvalidFileIdException('bad-id');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('bad-id')
        ->and($e->userMessage(fileTestTranslator()))->toBe('messages.exceptions.invalid_file_id:bad-id')
        ->and($e->invalidValue)->toBe('bad-id');
});

it('InvalidFileNameException has status 422 and translates', function (): void {
    $e = new InvalidFileNameException('');

    expect($e->statusCode())->toBe(422)
        ->and($e->userMessage(fileTestTranslator()))->toBe('messages.exceptions.invalid_file_name:')
        ->and($e->invalidValue)->toBe('');
});

it('InvalidFileNamespaceException has status 422 and translates', function (): void {
    $e = new InvalidFileNamespaceException('BAD NS');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('BAD NS')
        ->and($e->userMessage(fileTestTranslator()))->toBe('messages.exceptions.invalid_file_namespace:BAD NS')
        ->and($e->invalidValue)->toBe('BAD NS');
});

it('InvalidMimeTypeException has status 422 and translates', function (): void {
    $e = new InvalidMimeTypeException('bad-mime');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('bad-mime')
        ->and($e->userMessage(fileTestTranslator()))->toBe('messages.exceptions.invalid_mime_type:bad-mime')
        ->and($e->invalidValue)->toBe('bad-mime');
});

it('InvalidStoragePathException has status 422 and translates', function (): void {
    $e = new InvalidStoragePathException('');

    expect($e->statusCode())->toBe(422)
        ->and($e->userMessage(fileTestTranslator()))->toBe('messages.exceptions.invalid_storage_path:')
        ->and($e->invalidValue)->toBe('');
});

it('InvalidFileVersionException has status 422 and translates', function (): void {
    $e = new InvalidFileVersionException(0);

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('0')
        ->and($e->userMessage(fileTestTranslator()))->toBe('messages.exceptions.invalid_file_version:0')
        ->and($e->invalidValue)->toBe(0);
});

it('FileNotFoundException has status 404 and translates', function (): void {
    $e = new FileNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($e->statusCode())->toBe(404)
        ->and($e->getMessage())->toContain('550e8400-e29b-41d4-a716-446655440000')
        ->and($e->userMessage(fileTestTranslator()))->toBe('messages.exceptions.file_not_found:550e8400-e29b-41d4-a716-446655440000')
        ->and($e->id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('FileStorageException has status 422 and translates', function (): void {
    $e = new FileStorageException('docs/file.pdf');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('docs/file.pdf')
        ->and($e->userMessage(fileTestTranslator()))->toBe('messages.exceptions.file_storage_error:')
        ->and($e->path)->toBe('docs/file.pdf');
});

it('ImageProcessingException has status 422 and translates', function (): void {
    $e = new ImageProcessingException('resize failed');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('resize failed')
        ->and($e->userMessage(fileTestTranslator()))->toBe('messages.exceptions.image_processing_error:')
        ->and($e->detail)->toBe('resize failed');
});
