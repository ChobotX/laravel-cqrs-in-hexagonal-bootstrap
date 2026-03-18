<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../open-telemetry/context/./Context.php-PHPStan\BetterReflection\Reflection\ReflectionClass-OpenTelemetry\Context\Context
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-37f04d76f9dfaded518d603200bb683e2f63017012908ac9feb3e619c7b7083d-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'OpenTelemetry\\Context\\Context',
        'filename' => '/var/www/html/vendor/composer/../open-telemetry/context/./Context.php',
      ),
    ),
    'namespace' => 'OpenTelemetry\\Context',
    'name' => 'OpenTelemetry\\Context\\Context',
    'shortName' => 'Context',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/context/README.md#context
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 140,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'OpenTelemetry\\Context\\ContextInterface',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'OTEL_PHP_DEBUG_SCOPES_DISABLED' => 
      array (
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'name' => 'OTEL_PHP_DEBUG_SCOPES_DISABLED',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'OTEL_PHP_DEBUG_SCOPES_DISABLED\'',
          'attributes' => 
          array (
            'startLine' => 17,
            'endLine' => 17,
            'startTokenPos' => 65,
            'startFilePos' => 406,
            'endTokenPos' => 65,
            'endFilePos' => 437,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 84,
      ),
    ),
    'immediateProperties' => 
    array (
      'storage' => 
      array (
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'name' => 'storage',
        'modifiers' => 20,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionIntersectionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'name' => 'OpenTelemetry\\Context\\ContextStorageInterface',
                'isIdentifier' => false,
              ),
              1 => 
              array (
                'name' => 'OpenTelemetry\\Context\\ExecutionContextAwareInterface',
                'isIdentifier' => false,
              ),
            ),
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 83,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'spanContextKey' => 
      array (
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'name' => 'spanContextKey',
        'modifiers' => 20,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\Context\\ContextKeyInterface',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 55,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'span' => 
      array (
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'name' => 'span',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'object',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 99,
            'startFilePos' => 676,
            'endTokenPos' => 99,
            'endFilePos' => 679,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'context' => 
      array (
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'name' => 'context',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 112,
            'startFilePos' => 745,
            'endTokenPos' => 113,
            'endFilePos' => 746,
          ),
        ),
        'docComment' => '/** @var array<int, mixed> */',
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'contextKeys' => 
      array (
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'name' => 'contextKeys',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 126,
            'startFilePos' => 830,
            'endTokenPos' => 127,
            'endFilePos' => 831,
          ),
        ),
        'docComment' => '/** @var array<int, ContextKeyInterface> */',
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 36,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 29,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'createKey' => 
      array (
        'name' => 'createKey',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 35,
            'endLine' => 35,
            'startColumn' => 38,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\Context\\ContextKeyInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Override',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => NULL,
        'startLine' => 34,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'setStorage' => 
      array (
        'name' => 'setStorage',
        'parameters' => 
        array (
          'storage' => 
          array (
            'name' => 'storage',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionIntersectionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'name' => 'OpenTelemetry\\Context\\ContextStorageInterface',
                    'isIdentifier' => false,
                  ),
                  1 => 
                  array (
                    'name' => 'OpenTelemetry\\Context\\ExecutionContextAwareInterface',
                    'isIdentifier' => false,
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 39,
            'endColumn' => 101,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 40,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'storage' => 
      array (
        'name' => 'storage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionIntersectionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'name' => 'OpenTelemetry\\Context\\ContextStorageInterface',
                'isIdentifier' => false,
              ),
              1 => 
              array (
                'name' => 'OpenTelemetry\\Context\\ExecutionContextAwareInterface',
                'isIdentifier' => false,
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 45,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'resolve' => 
      array (
        'name' => 'resolve',
        'parameters' => 
        array (
          'context' => 
          array (
            'name' => 'context',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'OpenTelemetry\\Context\\ContextInterface',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'false',
                      'isIdentifier' => true,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 36,
            'endColumn' => 71,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'contextStorage' => 
          array (
            'name' => 'contextStorage',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 54,
                'endLine' => 54,
                'startTokenPos' => 282,
                'startFilePos' => 1650,
                'endTokenPos' => 282,
                'endFilePos' => 1653,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'OpenTelemetry\\Context\\ContextStorageInterface',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 74,
            'endColumn' => 120,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\Context\\ContextInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @internal OpenTelemetry
 */',
        'startLine' => 54,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'getRoot' => 
      array (
        'name' => 'getRoot',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\Context\\ContextInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @internal
 */',
        'startLine' => 64,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'getCurrent' => 
      array (
        'name' => 'getCurrent',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\Context\\ContextInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Override',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => NULL,
        'startLine' => 71,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'activate' => 
      array (
        'name' => 'activate',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\Context\\ScopeInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Override',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => NULL,
        'startLine' => 77,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'debugScopesDisabled' => 
      array (
        'name' => 'debugScopesDisabled',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 87,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'withContextValue' => 
      array (
        'name' => 'withContextValue',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 96,
            'endLine' => 96,
            'startColumn' => 38,
            'endColumn' => 73,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\Context\\ContextInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Override',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => NULL,
        'startLine' => 95,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'with' => 
      array (
        'name' => 'with',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'OpenTelemetry\\Context\\ContextKeyInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 102,
            'endLine' => 102,
            'startColumn' => 26,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 102,
            'endLine' => 102,
            'startColumn' => 52,
            'endColumn' => 57,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Override',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => NULL,
        'startLine' => 101,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
      'get' => 
      array (
        'name' => 'get',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'OpenTelemetry\\Context\\ContextKeyInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 131,
            'endLine' => 131,
            'startColumn' => 25,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Override',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => NULL,
        'startLine' => 130,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\Context',
        'implementingClassName' => 'OpenTelemetry\\Context\\Context',
        'currentClassName' => 'OpenTelemetry\\Context\\Context',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));