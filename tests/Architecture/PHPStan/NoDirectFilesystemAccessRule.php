<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function in_array;
use function str_contains;

/**
 * @implements Rule<Expr>
 */
final class NoDirectFilesystemAccessRule implements Rule
{
    private const array BANNED_FUNCTIONS = [
        'fopen',
        'fclose',
        'fread',
        'fwrite',
        'fgets',
        'feof',
        'fseek',
        'ftell',
        'fflush',
        'ftruncate',
        'flock',
        'file_get_contents',
        'file_put_contents',
        'file_exists',
        'file',
        'is_file',
        'is_dir',
        'unlink',
        'copy',
        'rename',
        'mkdir',
        'rmdir',
        'glob',
        'scandir',
        'tempnam',
        'tmpfile',
        'readfile',
        'storage_path',
    ];

    public function getNodeType(): string
    {
        return Expr::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if (! str_contains($file, '/app/') || str_contains($file, '/Infrastructure/Filesystem/')) {
            return [];
        }

        if ($node instanceof StaticCall) {
            return $this->checkStaticCall($node, $scope);
        }

        if ($node instanceof FuncCall) {
            return $this->checkFuncCall($node);
        }

        return [];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkStaticCall(StaticCall $staticCall, Scope $scope): array
    {
        if (! $staticCall->class instanceof Name) {
            return [];
        }

        $resolved = $scope->resolveName($staticCall->class);

        if ($resolved !== \Illuminate\Support\Facades\Storage::class) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Direct Storage:: facade usage is not allowed. Inject App\Domain\File\Contract\FileStorage instead.',
            )
                ->identifier('app.noDirectFilesystem')
                ->build(),
        ];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkFuncCall(FuncCall $funcCall): array
    {
        if (! $funcCall->name instanceof Name) {
            return [];
        }

        $name = $funcCall->name->toString();

        if (! in_array($name, self::BANNED_FUNCTIONS, true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Direct %s() usage is not allowed in application code. Use App\Domain\File\Contract\FileStorage instead.',
                $name,
            ))
                ->identifier('app.noDirectFilesystem')
                ->build(),
        ];
    }
}
