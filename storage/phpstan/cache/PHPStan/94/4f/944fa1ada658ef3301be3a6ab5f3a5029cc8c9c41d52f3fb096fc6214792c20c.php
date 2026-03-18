<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../open-telemetry/api/./Trace/TraceStateInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-OpenTelemetry\API\Trace\TraceStateInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0df15c67a1093db8b158c88a991008518a75ba71f5eda7d78380aaa91a14a8d7-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'filename' => '/var/www/html/vendor/composer/../open-telemetry/api/./Trace/TraceStateInterface.php',
      ),
    ),
    'namespace' => 'OpenTelemetry\\API\\Trace',
    'name' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
    'shortName' => 'TraceStateInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * TraceState parses and stores the tracestate header as an immutable list of string
 * key/value pairs. It provides the following operations following the rules described
 * in the W3C Trace Context specification:
 *      - Get value for a given key
 *      - Add a new key/value pair
 *      - Update an existing value for a given key
 *      - Delete a key/value pair
 *
 * All mutating operations return a new TraceState with the modifications applied.
 *
 * @see https://www.w3.org/TR/trace-context/#tracestate-header
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/master/specification/trace/api.md#tracestate
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 21,
    'endLine' => 67,
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 26,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 39,
            'endColumn' => 51,
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
            'name' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new TraceState object that inherits from this TraceState
 * and contains the given key value pair.
 *
 * @return TraceStateInterface
 */',
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 74,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'aliasName' => NULL,
      ),
      'without' => 
      array (
        'name' => 'without',
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
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 29,
            'endColumn' => 39,
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
            'name' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a new TraceState object that inherits from this TraceState
 * without the given key value pair.
 *
 * @return TraceStateInterface
 */',
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 62,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
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
            'startLine' => 44,
            'endLine' => 44,
            'startColumn' => 25,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
                  'name' => 'string',
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
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return the value of a given key from this TraceState if it exists
 *
 * @return string|null
 */',
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 46,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'aliasName' => NULL,
      ),
      'getListMemberCount' => 
      array (
        'name' => 'getListMemberCount',
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
 * Get the list-member count in this TraceState
 *
 * @return int
 */',
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 46,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'aliasName' => NULL,
      ),
      'toString' => 
      array (
        'name' => 'toString',
        'parameters' => 
        array (
          'limit' => 
          array (
            'name' => 'limit',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 61,
                'endLine' => 61,
                'startTokenPos' => 109,
                'startFilePos' => 1873,
                'endTokenPos' => 109,
                'endFilePos' => 1876,
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
                      'name' => 'int',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 30,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
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
        'docComment' => '/**
 * Returns the concatenated string representation.
 *
 * @param int|null $limit maximum length of the returned representation
 * @return string the string representation
 *
 * @see https://www.w3.org/TR/trace-context/#tracestate-limits
 */',
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 57,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'aliasName' => NULL,
      ),
      '__toString' => 
      array (
        'name' => '__toString',
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
        'docComment' => '/**
 * Returns a string representation of this TraceSate
 */',
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 41,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\TraceStateInterface',
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