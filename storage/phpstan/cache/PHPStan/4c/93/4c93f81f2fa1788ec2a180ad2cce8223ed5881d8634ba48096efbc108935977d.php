<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../open-telemetry/context/./ScopeInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-OpenTelemetry\Context\ScopeInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-a827c7740f55ca09d52f36356990988e1cf2e9d4218c5d325d2fb8eee43f9f83-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'OpenTelemetry\\Context\\ScopeInterface',
        'filename' => '/var/www/html/vendor/composer/../open-telemetry/context/./ScopeInterface.php',
      ),
    ),
    'namespace' => 'OpenTelemetry\\Context',
    'name' => 'OpenTelemetry\\Context\\ScopeInterface',
    'shortName' => 'ScopeInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
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
      'DETACHED' => 
      array (
        'declaringClassName' => 'OpenTelemetry\\Context\\ScopeInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ScopeInterface',
        'name' => 'DETACHED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '1 << (\\PHP_INT_SIZE << 3) - 1',
          'attributes' => 
          array (
            'startLine' => 12,
            'endLine' => 12,
            'startTokenPos' => 38,
            'startFilePos' => 203,
            'endTokenPos' => 52,
            'endFilePos' => 230,
          ),
        ),
        'docComment' => '/** The associated context was already detached. */',
        'attributes' => 
        array (
        ),
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'INACTIVE' => 
      array (
        'declaringClassName' => 'OpenTelemetry\\Context\\ScopeInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ScopeInterface',
        'name' => 'INACTIVE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '1 << (\\PHP_INT_SIZE << 3) - 2',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 14,
            'startTokenPos' => 65,
            'startFilePos' => 335,
            'endTokenPos' => 79,
            'endFilePos' => 362,
          ),
        ),
        'docComment' => '/** The associated context is not in the active execution context. */',
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'MISMATCH' => 
      array (
        'declaringClassName' => 'OpenTelemetry\\Context\\ScopeInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ScopeInterface',
        'name' => 'MISMATCH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '1 << (\\PHP_INT_SIZE << 3) - 3',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 92,
            'startFilePos' => 454,
            'endTokenPos' => 106,
            'endFilePos' => 481,
          ),
        ),
        'docComment' => '/** The associated context is not the active context. */',
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'detach' => 
      array (
        'name' => 'detach',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Detaches the context associated with this scope and restores the
 * previously associated context.
 *
 * @return int zero indicating an expected call, or a non-zero value
 *         indicating that the call was unexpected
 *
 * @see self::DETACHED
 * @see self::INACTIVE
 * @see self::MISMATCH
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/context/README.md#detach-context
 */',
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 34,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\Context',
        'declaringClassName' => 'OpenTelemetry\\Context\\ScopeInterface',
        'implementingClassName' => 'OpenTelemetry\\Context\\ScopeInterface',
        'currentClassName' => 'OpenTelemetry\\Context\\ScopeInterface',
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