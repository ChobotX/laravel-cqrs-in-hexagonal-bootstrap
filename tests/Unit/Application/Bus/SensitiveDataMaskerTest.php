<?php

declare(strict_types=1);

use App\Application\Bus\Sensitive;
use App\Application\Bus\SensitiveDataMasker;
use SplFileInfo;

enum TestBackedEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}

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

it('converts non-serializable objects to class names', function (): void {
    $file = new SplFileInfo('/tmp/test.png');
    $object = new readonly class($file)
    {
        public function __construct(
            public SplFileInfo $logo,
        ) {}
    };

    $result = SensitiveDataMasker::mask($object);

    expect($result['logo'])->toBe('/tmp/test.png');
});

it('converts backed enum values to their backing value', function (): void {
    $object = new readonly class(TestBackedEnum::Active)
    {
        public function __construct(
            public TestBackedEnum $status,
        ) {}
    };

    $result = SensitiveDataMasker::mask($object);

    expect($result['status'])->toBe('active');
});

it('converts non-stringable objects to class name', function (): void {
    $inner = new stdClass;
    $object = new readonly class($inner)
    {
        public function __construct(
            public stdClass $data,
        ) {}
    };

    $result = SensitiveDataMasker::mask($object);

    expect($result['data'])->toBe('stdClass');
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
