<?php

declare(strict_types=1);

use App\Application\Bus\Sensitive;
use App\Application\Bus\SensitiveDataMasker;

it('masks properties annotated with Sensitive', function (): void {
    $object = new readonly class('user-123', 's3cret!')
    {
        public function __construct(
            public string $userId,
            #[Sensitive]
            public string $rawPassword,
        ) {}
    };

    $result = SensitiveDataMasker::mask($object);

    expect($result)->toBe([
        'userId' => 'user-123',
        'rawPassword' => '***',
    ]);
});

it('returns all values when no properties are sensitive', function (): void {
    $object = new readonly class('user-123', 'john@example.com')
    {
        public function __construct(
            public string $userId,
            public string $email,
        ) {}
    };

    $result = SensitiveDataMasker::mask($object);

    expect($result)->toBe([
        'userId' => 'user-123',
        'email' => 'john@example.com',
    ]);
});

it('masks all values when all properties are sensitive', function (): void {
    $object = new readonly class('s3cret!', 'token-abc')
    {
        public function __construct(
            #[Sensitive]
            public string $password,
            #[Sensitive]
            public string $token,
        ) {}
    };

    $result = SensitiveDataMasker::mask($object);

    expect($result)->toBe([
        'password' => '***',
        'token' => '***',
    ]);
});

it('returns empty array for object with no public properties', function (): void {
    $object = new stdClass;

    $result = SensitiveDataMasker::mask($object);

    expect($result)->toBe([]);
});

it('masks nullable sensitive properties', function (): void {
    $object = new readonly class('user-123', null)
    {
        public function __construct(
            public string $userId,
            #[Sensitive]
            public ?string $rawPassword,
        ) {}
    };

    $result = SensitiveDataMasker::mask($object);

    expect($result)->toBe([
        'userId' => 'user-123',
        'rawPassword' => '***',
    ]);
});

it('preserves non-string property types', function (): void {
    $object = new readonly class(42, true, ['a', 'b'])
    {
        public function __construct(
            public int $count,
            public bool $active,
            /** @var list<string> */
            public array $tags,
        ) {}
    };

    $result = SensitiveDataMasker::mask($object);

    expect($result)->toBe([
        'count' => 42,
        'active' => true,
        'tags' => ['a', 'b'],
    ]);
});
