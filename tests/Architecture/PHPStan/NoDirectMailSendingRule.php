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

use function str_starts_with;

/**
 * @implements Rule<Expr>
 */
final class NoDirectMailSendingRule implements Rule
{
    private const string MAIL_FACADE = \Illuminate\Support\Facades\Mail::class;

    private const string TEMPLATE_SENDER_CLASS = \App\Infrastructure\EmailTemplate\LaravelEmailSender::class;

    public function getNodeType(): string
    {
        return Expr::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\')) {
            return [];
        }

        $className = $scope->getClassReflection()?->getName();

        if ($className === self::TEMPLATE_SENDER_CLASS) {
            return [];
        }

        if ($node instanceof StaticCall) {
            return $this->checkStaticCall($node, $scope);
        }

        if ($node instanceof FuncCall) {
            return $this->checkFuncCall($node, $scope);
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

        if ($scope->resolveName($staticCall->class) !== self::MAIL_FACADE) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Direct Mail facade usage is forbidden. Send system emails via templated dispatcher (TemplatedEmailDispatcher).',
            )
                ->identifier('app.noDirectMailSending')
                ->build(),
        ];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkFuncCall(FuncCall $funcCall, Scope $scope): array
    {
        if (! $funcCall->name instanceof Name) {
            return [];
        }

        $functionName = $funcCall->name->toString();

        if ($functionName !== 'mail') {
            return [];
        }

        $namespace = $scope->getNamespace();

        if ($namespace !== null && str_starts_with($namespace, 'Tests\\')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Direct mail() usage is forbidden. Send system emails via templated dispatcher (TemplatedEmailDispatcher).',
            )
                ->identifier('app.noDirectMailSending')
                ->build(),
        ];
    }
}
