<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Cache/ArrayStore.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Cache\ArrayStore
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-26f30b3dbc1500d9ca904a083c2c94c45eeb036c0d2127a43aaf1e29a4c46a8e-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Cache\\ArrayStore',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Cache/ArrayStore.php',
      ),
    ),
    'namespace' => 'Illuminate\\Cache',
    'name' => 'Illuminate\\Cache\\ArrayStore',
    'shortName' => 'ArrayStore',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 266,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Cache\\TaggableStore',
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Cache\\LockProvider',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\InteractsWithTime',
      1 => 'Illuminate\\Cache\\RetrievesMultipleKeys',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'storage' => 
      array (
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'name' => 'storage',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 52,
            'startFilePos' => 419,
            'endTokenPos' => 53,
            'endFilePos' => 420,
          ),
        ),
        'docComment' => '/**
 * The array of stored values.
 *
 * @var array<string, array{value: mixed, expiresAt: float}>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'locks' => 
      array (
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'name' => 'locks',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 64,
            'startFilePos' => 583,
            'endTokenPos' => 65,
            'endFilePos' => 584,
          ),
        ),
        'docComment' => '/**
 * The array of locks.
 *
 * @var array<string, array{owner: ?string, expiresAt: ?\\Illuminate\\Support\\Carbon}>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'serializesValues' => 
      array (
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'name' => 'serializesValues',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * Indicates if values are serialized within the store.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'serializableClasses' => 
      array (
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'name' => 'serializableClasses',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The classes that should be allowed during unserialization.
 *
 * @var array|bool|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 35,
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
          'serializesValues' => 
          array (
            'name' => 'serializesValues',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 47,
                'endLine' => 47,
                'startTokenPos' => 94,
                'startFilePos' => 1076,
                'endTokenPos' => 94,
                'endFilePos' => 1080,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 47,
            'endLine' => 47,
            'startColumn' => 33,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'serializableClasses' => 
          array (
            'name' => 'serializableClasses',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 47,
                'endLine' => 47,
                'startTokenPos' => 101,
                'startFilePos' => 1106,
                'endTokenPos' => 101,
                'endFilePos' => 1109,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 47,
            'endLine' => 47,
            'startColumn' => 60,
            'endColumn' => 86,
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
 * Create a new Array store.
 *
 * @param  bool  $serializesValues
 * @param  array|bool|null  $serializableClasses
 */',
        'startLine' => 47,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'all' => 
      array (
        'name' => 'all',
        'parameters' => 
        array (
          'unserialize' => 
          array (
            'name' => 'unserialize',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 59,
                'endLine' => 59,
                'startTokenPos' => 138,
                'startFilePos' => 1465,
                'endTokenPos' => 138,
                'endFilePos' => 1468,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 59,
            'endLine' => 59,
            'startColumn' => 25,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all of the cached values and their expiration times.
 *
 * @param  bool  $unserialize
 * @return array<string, array{value: mixed, expiresAt: float}>
 */',
        'startLine' => 59,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
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
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 25,
            'endColumn' => 28,
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
 * Retrieve an item from the cache by key.
 *
 * @param  string  $key
 * @return mixed
 */',
        'startLine' => 83,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'put' => 
      array (
        'name' => 'put',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 25,
            'endColumn' => 28,
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
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 31,
            'endColumn' => 36,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'seconds' => 
          array (
            'name' => 'seconds',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 110,
            'endLine' => 110,
            'startColumn' => 39,
            'endColumn' => 46,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Store an item in the cache for a given number of seconds.
 *
 * @param  string  $key
 * @param  mixed  $value
 * @param  int  $seconds
 * @return bool
 */',
        'startLine' => 110,
        'endLine' => 118,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'increment' => 
      array (
        'name' => 'increment',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 127,
            'endLine' => 127,
            'startColumn' => 31,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 127,
                'endLine' => 127,
                'startTokenPos' => 471,
                'startFilePos' => 3115,
                'endTokenPos' => 471,
                'endFilePos' => 3115,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 127,
            'endLine' => 127,
            'startColumn' => 37,
            'endColumn' => 46,
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
 * Increment the value of an item in the cache.
 *
 * @param  string  $key
 * @param  mixed  $value
 * @return int
 */',
        'startLine' => 127,
        'endLine' => 140,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'decrement' => 
      array (
        'name' => 'decrement',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 149,
            'endLine' => 149,
            'startColumn' => 31,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 149,
                'endLine' => 149,
                'startTokenPos' => 601,
                'startFilePos' => 3707,
                'endTokenPos' => 601,
                'endFilePos' => 3707,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 149,
            'endLine' => 149,
            'startColumn' => 37,
            'endColumn' => 46,
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
 * Decrement the value of an item in the cache.
 *
 * @param  string  $key
 * @param  mixed  $value
 * @return int
 */',
        'startLine' => 149,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'forever' => 
      array (
        'name' => 'forever',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 161,
            'endLine' => 161,
            'startColumn' => 29,
            'endColumn' => 32,
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
            'startLine' => 161,
            'endLine' => 161,
            'startColumn' => 35,
            'endColumn' => 40,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Store an item in the cache indefinitely.
 *
 * @param  string  $key
 * @param  mixed  $value
 * @return bool
 */',
        'startLine' => 161,
        'endLine' => 164,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'forget' => 
      array (
        'name' => 'forget',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 28,
            'endColumn' => 31,
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
 * Remove an item from the cache.
 *
 * @param  string  $key
 * @return bool
 */',
        'startLine' => 172,
        'endLine' => 181,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'flush' => 
      array (
        'name' => 'flush',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Remove all items from the cache.
 *
 * @return bool
 */',
        'startLine' => 188,
        'endLine' => 193,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'getPrefix' => 
      array (
        'name' => 'getPrefix',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the cache key prefix.
 *
 * @return string
 */',
        'startLine' => 200,
        'endLine' => 203,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'calculateExpiration' => 
      array (
        'name' => 'calculateExpiration',
        'parameters' => 
        array (
          'seconds' => 
          array (
            'name' => 'seconds',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 211,
            'endLine' => 211,
            'startColumn' => 44,
            'endColumn' => 51,
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
 * Get the expiration time of the key.
 *
 * @param  int  $seconds
 * @return float
 */',
        'startLine' => 211,
        'endLine' => 214,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'toTimestamp' => 
      array (
        'name' => 'toTimestamp',
        'parameters' => 
        array (
          'seconds' => 
          array (
            'name' => 'seconds',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 222,
            'endLine' => 222,
            'startColumn' => 36,
            'endColumn' => 43,
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
 * Get the UNIX timestamp, with milliseconds, for the given number of seconds in the future.
 *
 * @param  int  $seconds
 * @return float
 */',
        'startLine' => 222,
        'endLine' => 225,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'lock' => 
      array (
        'name' => 'lock',
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
            'startLine' => 235,
            'endLine' => 235,
            'startColumn' => 26,
            'endColumn' => 30,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'seconds' => 
          array (
            'name' => 'seconds',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 235,
                'endLine' => 235,
                'startTokenPos' => 853,
                'startFilePos' => 5431,
                'endTokenPos' => 853,
                'endFilePos' => 5431,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 235,
            'endLine' => 235,
            'startColumn' => 33,
            'endColumn' => 44,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'owner' => 
          array (
            'name' => 'owner',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 235,
                'endLine' => 235,
                'startTokenPos' => 860,
                'startFilePos' => 5443,
                'endTokenPos' => 860,
                'endFilePos' => 5446,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 235,
            'endLine' => 235,
            'startColumn' => 47,
            'endColumn' => 59,
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
 * Get a lock instance.
 *
 * @param  string  $name
 * @param  int  $seconds
 * @param  string|null  $owner
 * @return \\Illuminate\\Contracts\\Cache\\Lock
 */',
        'startLine' => 235,
        'endLine' => 238,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'restoreLock' => 
      array (
        'name' => 'restoreLock',
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
            'startLine' => 247,
            'endLine' => 247,
            'startColumn' => 33,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'owner' => 
          array (
            'name' => 'owner',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 247,
            'endLine' => 247,
            'startColumn' => 40,
            'endColumn' => 45,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Restore a lock instance using the owner identifier.
 *
 * @param  string  $name
 * @param  string  $owner
 * @return \\Illuminate\\Contracts\\Cache\\Lock
 */',
        'startLine' => 247,
        'endLine' => 250,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
        'aliasName' => NULL,
      ),
      'unserialize' => 
      array (
        'name' => 'unserialize',
        'parameters' => 
        array (
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
            'startLine' => 258,
            'endLine' => 258,
            'startColumn' => 36,
            'endColumn' => 41,
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
 * Unserialize the given value.
 *
 * @param  string  $value
 * @return mixed
 */',
        'startLine' => 258,
        'endLine' => 265,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Cache',
        'declaringClassName' => 'Illuminate\\Cache\\ArrayStore',
        'implementingClassName' => 'Illuminate\\Cache\\ArrayStore',
        'currentClassName' => 'Illuminate\\Cache\\ArrayStore',
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