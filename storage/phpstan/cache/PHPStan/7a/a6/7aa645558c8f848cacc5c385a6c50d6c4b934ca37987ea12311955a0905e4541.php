<?php declare(strict_types = 1);

// odsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/helpers.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-resolve
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-3fe9475e8c073a9c58af7e9cb8c74416031941413358f6082d558134d416ba86',
   'data' => 
  array (
    'name' => 'resolve',
    'parameters' => 
    array (
      'name' => 
      array (
        'name' => 'name',
        'default' => NULL,
        'type' => NULL,
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 822,
        'endLine' => 822,
        'startColumn' => 22,
        'endColumn' => 26,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'parameters' => 
      array (
        'name' => 'parameters',
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 822,
            'endLine' => 822,
            'startTokenPos' => 3652,
            'startFilePos' => 22160,
            'endTokenPos' => 3653,
            'endFilePos' => 22161,
          ),
        ),
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'isVariadic' => false,
        'byRef' => false,
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 822,
        'endLine' => 822,
        'startColumn' => 29,
        'endColumn' => 50,
        'parameterIndex' => 1,
        'isOptional' => true,
      ),
    ),
    'returnsReference' => false,
    'returnType' => NULL,
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Resolve a service from the container.
 *
 * @template TClass of object
 *
 * @param  string|class-string<TClass>  $name
 * @return ($name is class-string<TClass> ? TClass : mixed)
 */',
    'startLine' => 822,
    'endLine' => 825,
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
        'name' => 'resolve',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Foundation/helpers.php',
      ),
    ),
  ),
));