<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../open-telemetry/api/./Trace/SpanContextInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-OpenTelemetry\API\Trace\SpanContextInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-cd97f204008810998722e77e3c7c5b09abcf4b3e7789ab34d857d32edc5ba7bd-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'filename' => '/var/www/html/vendor/composer/../open-telemetry/api/./Trace/SpanContextInterface.php',
      ),
    ),
    'namespace' => 'OpenTelemetry\\API\\Trace',
    'name' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
    'shortName' => 'SpanContextInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#spancontext
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 28,
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
      'createFromRemoteParent' => 
      array (
        'name' => 'createFromRemoteParent',
        'parameters' => 
        array (
          'traceId' => 
          array (
            'name' => 'traceId',
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
            'startLine' => 12,
            'endLine' => 12,
            'startColumn' => 51,
            'endColumn' => 65,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'spanId' => 
          array (
            'name' => 'spanId',
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
            'startLine' => 12,
            'endLine' => 12,
            'startColumn' => 68,
            'endColumn' => 81,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'traceFlags' => 
          array (
            'name' => 'traceFlags',
            'default' => 
            array (
              'code' => '\\OpenTelemetry\\API\\Trace\\TraceFlags::DEFAULT',
              'attributes' => 
              array (
                'startLine' => 12,
                'endLine' => 12,
                'startTokenPos' => 47,
                'startFilePos' => 332,
                'endTokenPos' => 49,
                'endFilePos' => 350,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 12,
            'endLine' => 12,
            'startColumn' => 84,
            'endColumn' => 120,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'traceState' => 
          array (
            'name' => 'traceState',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 12,
                'endLine' => 12,
                'startTokenPos' => 59,
                'startFilePos' => 388,
                'endTokenPos' => 59,
                'endFilePos' => 391,
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
                      'name' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
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
            'startLine' => 12,
            'endLine' => 12,
            'startColumn' => 123,
            'endColumn' => 161,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 5,
        'endColumn' => 185,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'getInvalid' => 
      array (
        'name' => 'getInvalid',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 13,
        'endLine' => 13,
        'startColumn' => 5,
        'endColumn' => 62,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'create' => 
      array (
        'name' => 'create',
        'parameters' => 
        array (
          'traceId' => 
          array (
            'name' => 'traceId',
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
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 35,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'spanId' => 
          array (
            'name' => 'spanId',
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
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 52,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'traceFlags' => 
          array (
            'name' => 'traceFlags',
            'default' => 
            array (
              'code' => '\\OpenTelemetry\\API\\Trace\\TraceFlags::DEFAULT',
              'attributes' => 
              array (
                'startLine' => 14,
                'endLine' => 14,
                'startTokenPos' => 104,
                'startFilePos' => 565,
                'endTokenPos' => 106,
                'endFilePos' => 583,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 68,
            'endColumn' => 104,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'traceState' => 
          array (
            'name' => 'traceState',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 14,
                'endLine' => 14,
                'startTokenPos' => 116,
                'startFilePos' => 621,
                'endTokenPos' => 116,
                'endFilePos' => 624,
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
                      'name' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
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
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 107,
            'endColumn' => 145,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 5,
        'endColumn' => 169,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'getTraceId' => 
      array (
        'name' => 'getTraceId',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @psalm-mutation-free */',
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 41,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'getTraceIdBinary' => 
      array (
        'name' => 'getTraceIdBinary',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 47,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'getSpanId' => 
      array (
        'name' => 'getSpanId',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @psalm-mutation-free */',
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 40,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'getSpanIdBinary' => 
      array (
        'name' => 'getSpanIdBinary',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 46,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'getTraceFlags' => 
      array (
        'name' => 'getTraceFlags',
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
        'docComment' => NULL,
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 41,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'getTraceState' => 
      array (
        'name' => 'getTraceState',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
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
                  'name' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
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
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 58,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'isValid' => 
      array (
        'name' => 'isValid',
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
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 36,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'isRemote' => 
      array (
        'name' => 'isRemote',
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
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 37,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'aliasName' => NULL,
      ),
      'isSampled' => 
      array (
        'name' => 'isSampled',
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
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 38,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
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