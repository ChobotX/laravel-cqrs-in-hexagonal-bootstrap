<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../open-telemetry/api/./Trace/SpanInterface.php-PHPStan\BetterReflection\Reflection\ReflectionClass-OpenTelemetry\API\Trace\SpanInterface
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-2efff6b925f5283e286857a3aa96f5c7192afb489b1dd2f3b56f43d6188fad70-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'filename' => '/var/www/html/vendor/composer/../open-telemetry/api/./Trace/SpanInterface.php',
      ),
    ),
    'namespace' => 'OpenTelemetry\\API\\Trace',
    'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
    'shortName' => 'SpanInterface',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#span-operations
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 111,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'OpenTelemetry\\Context\\ImplicitContextKeyedInterface',
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
      'fromContext' => 
      array (
        'name' => 'fromContext',
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
            'startLine' => 20,
            'endLine' => 20,
            'startColumn' => 40,
            'endColumn' => 64,
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns the {@see SpanInterface} from the provided *$context*,
 * falling back on {@see SpanInterface::getInvalid()} if there is no span in the provided context.
 */',
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 81,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns the current {@see SpanInterface} from the current {@see ContextInterface},
 * falling back on {@see SpanInterface::getEmpty()} if there is no span in the current context.
 */',
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 55,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns an invalid {@see SpanInterface} that is used when tracing is disabled, such s when there is no available SDK.
 */',
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 55,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'wrap' => 
      array (
        'name' => 'wrap',
        'parameters' => 
        array (
          'spanContext' => 
          array (
            'name' => 'spanContext',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 39,
            'endLine' => 39,
            'startColumn' => 33,
            'endColumn' => 65,
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Returns a non-recording {@see SpanInterface} that hold the provided *$spanContext* but has no functionality.
 * It will not be exported and al tracing operations are no-op, but can be used to propagate a valid {@see SpanContext} downstream.
 *
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#wrapping-a-spancontext-in-a-span
 */',
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 82,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'getContext' => 
      array (
        'name' => 'getContext',
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
        'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#get-context
 */',
        'startLine' => 44,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 55,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'isRecording' => 
      array (
        'name' => 'isRecording',
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
        'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#isrecording
 */',
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 40,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'setAttribute' => 
      array (
        'name' => 'setAttribute',
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
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 34,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
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
                      'name' => 'bool',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'int',
                      'isIdentifier' => true,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'float',
                      'isIdentifier' => true,
                    ),
                  ),
                  3 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  4 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  5 => 
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
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 47,
            'endColumn' => 85,
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#set-attributes
 * Adding attributes at span creation is preferred to calling setAttribute later, as samplers can only consider information
 * already present during span creation
 * @param non-empty-string $key
 * @param bool|int|float|string|array|null $value Note: arrays MUST be homogeneous, i.e. it MUST NOT contain values of different types.
 */',
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 102,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'setAttributes' => 
      array (
        'name' => 'setAttributes',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'iterable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 66,
            'endLine' => 66,
            'startColumn' => 35,
            'endColumn' => 54,
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#set-attributes
 * An attribute with a null key will be dropped, and an attribute with a null value will be dropped but also remove any existing
 * attribute with the same key.
 * @param iterable<non-empty-string, bool|int|float|string|array|null> $attributes
 */',
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 71,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'addLink' => 
      array (
        'name' => 'addLink',
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
                'name' => 'OpenTelemetry\\API\\Trace\\SpanContextInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 81,
            'endLine' => 81,
            'startColumn' => 29,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 81,
                'endLine' => 81,
                'startTokenPos' => 208,
                'startFilePos' => 3793,
                'endTokenPos' => 209,
                'endFilePos' => 3794,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'iterable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 81,
            'endLine' => 81,
            'startColumn' => 60,
            'endColumn' => 84,
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Records a link to another `SpanContext`.
 *
 * Adding links at span creation via {@link SpanBuilderInterface::addLink()} is preferred to calling
 * {@link SpanInterface::addLink()} later, for contexts that are available during span creation, because head
 * sampling decisions can only consider information present during span creation.
 *
 * @param SpanContextInterface $context span context to link
 * @param iterable $attributes attributes to associate with the link
 * @return SpanInterface this span
 *
 * @see https://opentelemetry.io/docs/specs/otel/trace/api/#add-link
 */',
        'startLine' => 81,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 101,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'addEvent' => 
      array (
        'name' => 'addEvent',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 86,
            'endLine' => 86,
            'startColumn' => 30,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 86,
                'endLine' => 86,
                'startTokenPos' => 235,
                'startFilePos' => 4020,
                'endTokenPos' => 236,
                'endFilePos' => 4021,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'iterable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 86,
            'endLine' => 86,
            'startColumn' => 44,
            'endColumn' => 68,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'timestamp' => 
          array (
            'name' => 'timestamp',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 86,
                'endLine' => 86,
                'startTokenPos' => 246,
                'startFilePos' => 4042,
                'endTokenPos' => 246,
                'endFilePos' => 4045,
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
            'startLine' => 86,
            'endLine' => 86,
            'startColumn' => 71,
            'endColumn' => 92,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#add-events
 */',
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 109,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'recordException' => 
      array (
        'name' => 'recordException',
        'parameters' => 
        array (
          'exception' => 
          array (
            'name' => 'exception',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Throwable',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 91,
            'endLine' => 91,
            'startColumn' => 37,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 91,
                'endLine' => 91,
                'startTokenPos' => 272,
                'startFilePos' => 4292,
                'endTokenPos' => 273,
                'endFilePos' => 4293,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'iterable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 91,
            'endLine' => 91,
            'startColumn' => 59,
            'endColumn' => 83,
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#record-exception
 */',
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 100,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'updateName' => 
      array (
        'name' => 'updateName',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 98,
            'endLine' => 98,
            'startColumn' => 32,
            'endColumn' => 43,
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#updatename
 *
 * @param non-empty-string $name
 */',
        'startLine' => 98,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 60,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'setStatus' => 
      array (
        'name' => 'setStatus',
        'parameters' => 
        array (
          'code' => 
          array (
            'name' => 'code',
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
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 31,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'description' => 
          array (
            'name' => 'description',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 105,
                'endLine' => 105,
                'startTokenPos' => 317,
                'startFilePos' => 4820,
                'endTokenPos' => 317,
                'endFilePos' => 4823,
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 45,
            'endColumn' => 71,
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
            'name' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#set-status
 *
 * @psalm-param StatusCode::STATUS_* $code
 */',
        'startLine' => 105,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 88,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'aliasName' => NULL,
      ),
      'end' => 
      array (
        'name' => 'end',
        'parameters' => 
        array (
          'endEpochNanos' => 
          array (
            'name' => 'endEpochNanos',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 110,
                'endLine' => 110,
                'startTokenPos' => 339,
                'startFilePos' => 5022,
                'endTokenPos' => 339,
                'endFilePos' => 5025,
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
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 25,
            'endColumn' => 50,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.6.1/specification/trace/api.md#end
 */',
        'startLine' => 110,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 58,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'OpenTelemetry\\API\\Trace',
        'declaringClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'implementingClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
        'currentClassName' => 'OpenTelemetry\\API\\Trace\\SpanInterface',
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