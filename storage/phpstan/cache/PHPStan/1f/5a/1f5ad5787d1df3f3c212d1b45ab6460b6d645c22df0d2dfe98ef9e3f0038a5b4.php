<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../open-telemetry/context/./ImplicitContextKeyedInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-OpenTelemetry\Context\ImplicitContextKeyedInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-95e3e453636493e25c91e486eaa79842573b979f2a4cc216dce475aacff25d7c-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
        'filename' => '/var/www/html/vendor/composer/../open-telemetry/context/./ImplicitContextKeyedInterface.php',
      ),
    ),
    'namespace' => 'OpenTelemetry\\Context',
    'name' => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
    'shortName' => 'ImplicitContextKeyedInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Represents a value that can be stored within {@see ContextInterface}.
 * Allows storing themselves without exposing a {@see ContextKeyInterface}.
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#context-interaction
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/baggage/api.md#context-interaction
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 32,
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
 * Adds `$this` to the {@see Context::getCurrent() current context} and makes
 * the new {@see Context} the current context.
 *
 * {@see ScopeInterface::detach()} _MUST_ be called to properly restore the previous context.
 *
 * This method is equivalent to `Context::getCurrent().with($value).activate();`.
 *
 * @todo: Update this to suggest using the new helper method way to doing something in a specific context/span.
 */',
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 47,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
        'currentClassName' => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
        'aliasName' => NULL,
      ),
      'storeInContext' => 
      array (
        'name' => 'storeInContext',
        'parameters' => 
        array (
          'context' => 
          array (
            'name' => 'context',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'OpenTelemetry\\Context\\ContextInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 36,
            'endColumn' => 60,
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
 * Returns a new {@see ContextInterface} created by setting `$this` into the provided [@see ContextInterface}.
 */',
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 80,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
        'currentClassName' => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
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