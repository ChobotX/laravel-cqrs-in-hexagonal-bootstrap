<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../open-telemetry/context/./ContextInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-OpenTelemetry\Context\ContextInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-370381de87557f2e23202698e4d656e8ab2e9cda682d029ef8a6aa70f6be0ce8-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'OpenTelemetry\\Context\\ContextInterface',
        'filename' => '/var/www/html/vendor/composer/../open-telemetry/context/./ContextInterface.php',
      ),
    ),
    'namespace' => 'OpenTelemetry\\Context',
    'name' => 'OpenTelemetry\\Context\\ContextInterface',
    'shortName' => 'ContextInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Immutable execution scoped propagation mechanism.
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/context/README.md#context
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 86,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
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
            'startLine' => 22,
            'endLine' => 22,
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
        ),
        'docComment' => '/**
 * Creates a new context key.
 *
 * @param non-empty-string $key name of the key
 * @return ContextKeyInterface created key
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/context/README.md#create-a-key
 */',
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 71,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'currentClassName' => 'OpenTelemetry\\Context\\ContextInterface',
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
        ),
        'docComment' => '/**
 * Returns the current context.
 *
 * @return ContextInterface current context
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/context/README.md#get-current-context
 */',
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 58,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'currentClassName' => 'OpenTelemetry\\Context\\ContextInterface',
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
        ),
        'docComment' => '/**
 * Attaches this context as active context.
 *
 * The returned scope has to be {@link ScopeInterface::detach()}ed. In most
 * cases this should be done using a `try-finally` statement:
 * ```php
 * $scope = $context->activate();
 * try {
 *     // ...
 * } finally {
 *     $scope->detach();
 * }
 * ```
 *
 * @return ScopeInterface scope to detach the context and restore the previous
 *         context
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/context/README.md#attach-context
 */',
        'startLine' => 52,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 47,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'currentClassName' => 'OpenTelemetry\\Context\\ContextInterface',
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
            'startLine' => 64,
            'endLine' => 64,
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
            'startLine' => 64,
            'endLine' => 64,
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
            'name' => 'OpenTelemetry\\Context\\ContextInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns a context with the given key set to the given value.
 *
 * @template T
 * @param ContextKeyInterface<T> $key key to set
 * @param T|null $value value to set
 * @return ContextInterface a context with the given key set to `$value`
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/context/README.md#set-value
 */',
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 77,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'currentClassName' => 'OpenTelemetry\\Context\\ContextInterface',
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
            'startLine' => 74,
            'endLine' => 74,
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
        ),
        'docComment' => '/**
 * Returns a context with the given value set.
 *
 * @param ImplicitContextKeyedInterface $value value to set
 * @return ContextInterface a context with the given `$value`
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/context/README.md#set-value
 */',
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 93,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'currentClassName' => 'OpenTelemetry\\Context\\ContextInterface',
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
            'startLine' => 85,
            'endLine' => 85,
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
        ),
        'docComment' => '/**
 * Returns the value assigned to the given key.
 *
 * @template T
 * @param ContextKeyInterface<T> $key key to get
 * @return T|null value assigned to `$key`, or null if no such value exists
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/context/README.md#get-value
 */',
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 50,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ContextInterface',
        'currentClassName' => 'OpenTelemetry\\Context\\ContextInterface',
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