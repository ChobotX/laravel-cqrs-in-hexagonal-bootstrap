<?php declare(strict_types = 1);

// odsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Support/helpers.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-throw_unless
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-03e08f2db7a9486e64b7887f9a1c50b544b68fe8794284259fa985a40534a5b8',
   'data' => 
  array (
    'name' => 'throw_unless',
    'parameters' => 
    array (
      'condition' => 
      array (
        'name' => 'condition',
        'default' => NULL,
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 444,
        'endLine' => 444,
        'startColumn' => 27,
        'endColumn' => 36,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'exception' => 
      array (
        'name' => 'exception',
        'default' => 
        array (
          'code' => '\'RuntimeException\'',
          'attributes' => 
          array (
            'startLine' => 444,
            'endLine' => 444,
            'startTokenPos' => 1887,
            'startFilePos' => 11529,
            'endTokenPos' => 1887,
            'endFilePos' => 11546,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 444,
        'endLine' => 444,
        'startColumn' => 39,
        'endColumn' => 69,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
      'parameters' => 
      array (
        'name' => 'parameters',
        'default' => NULL,
        'type' => NULL,
        'isVariadic' => true,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 444,
        'endLine' => 444,
        'startColumn' => 72,
        'endColumn' => 85,
        'parameterIndex' => 2,
        'isOptional' => true,
      ),
    ),
    'returnsReference' => false,
    'returnType' => NULL,
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Throw the given exception unless the given condition is true.
 *
 * @template TValue
 * @template TParams of mixed
 * @template TException of \\Throwable
 * @template TExceptionValue of TException|class-string<TException>|string
 *
 * @param  TValue  $condition
 * @param  Closure(TParams): TExceptionValue|TExceptionValue  $exception
 * @param  TParams  ...$parameters
 * @return ($condition is false ? never : ($condition is non-empty-mixed ? TValue : never))
 *
 * @throws TException
 */',
    'startLine' => 444,
    'endLine' => 449,
    'startColumn' => 5,
    'endColumn' => 5,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => true,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'throw_unless',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Support/helpers.php',
      ),
    ),
  ),
));