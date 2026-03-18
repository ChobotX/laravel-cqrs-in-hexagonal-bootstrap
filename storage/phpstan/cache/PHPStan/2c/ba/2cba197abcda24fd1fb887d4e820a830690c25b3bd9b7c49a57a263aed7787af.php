<?php declare(strict_types = 1);

// odsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/helpers.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-rescue
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-3fe9475e8c073a9c58af7e9cb8c74416031941413358f6082d558134d416ba86',
   'data' => 
  array (
    'name' => 'rescue',
    'parameters' => 
    array (
      'callback' => 
      array (
        'name' => 'callback',
        'default' => NULL,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'callable',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 799,
        'endLine' => 799,
        'startColumn' => 21,
        'endColumn' => 38,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'rescue' => 
      array (
        'name' => 'rescue',
        'default' => 
        array (
          'code' => '\\null',
          'attributes' => 
          array (
            'startLine' => 799,
            'endLine' => 799,
            'startTokenPos' => 3550,
            'startFilePos' => 21607,
            'endTokenPos' => 3550,
            'endFilePos' => 21610,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 799,
        'endLine' => 799,
        'startColumn' => 41,
        'endColumn' => 54,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
      'report' => 
      array (
        'name' => 'report',
        'default' => 
        array (
          'code' => '\\true',
          'attributes' => 
          array (
            'startLine' => 799,
            'endLine' => 799,
            'startTokenPos' => 3557,
            'startFilePos' => 21623,
            'endTokenPos' => 3557,
            'endFilePos' => 21626,
          ),
        ),
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 799,
        'endLine' => 799,
        'startColumn' => 57,
        'endColumn' => 70,
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
 * Catch a potential exception and return a default value.
 *
 * @template TValue
 * @template TFallback
 *
 * @param  callable(): TValue  $callback
 * @param  (callable(\\Throwable): TFallback)|TFallback  $rescue
 * @param  bool|callable(\\Throwable): bool  $report
 * @return TValue|TFallback
 */',
    'startLine' => 799,
    'endLine' => 810,
    'startColumn' => 5,
    'endColumn' => 5,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'rescue',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/helpers.php',
      ),
    ),
  ),
));