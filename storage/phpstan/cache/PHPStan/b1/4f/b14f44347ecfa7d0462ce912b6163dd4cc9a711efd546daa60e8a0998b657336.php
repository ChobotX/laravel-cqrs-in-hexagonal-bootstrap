<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Queue/DatabaseQueue.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Queue\DatabaseQueue
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-70afabf220661e103d6242cca5273ed0cbf938320e2274459c0360bffa372c74-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Queue\\DatabaseQueue',
        'filename' => '/var/www/html/vendor/composer/../laravel/framework/src/Illuminate/Queue/DatabaseQueue.php',
      ),
    ),
    'namespace' => 'Illuminate\\Queue',
    'name' => 'Illuminate\\Queue\\DatabaseQueue',
    'shortName' => 'DatabaseQueue',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 500,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Queue\\Queue',
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Queue\\Queue',
      1 => 'Illuminate\\Contracts\\Queue\\ClearableQueue',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'database' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'name' => 'database',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The database connection instance.
 *
 * @var \\Illuminate\\Database\\Connection
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'table' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The database table that holds the jobs.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'default' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'name' => 'default',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The name of the default queue.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'retryAfter' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'name' => 'retryAfter',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '60',
          'attributes' => 
          array (
            'startLine' => 45,
            'endLine' => 45,
            'startTokenPos' => 112,
            'startFilePos' => 949,
            'endTokenPos' => 112,
            'endFilePos' => 950,
          ),
        ),
        'docComment' => '/**
 * The expiration time of a job.
 *
 * @var int|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 31,
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
          'database' => 
          array (
            'name' => 'database',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Connection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 57,
            'endLine' => 57,
            'startColumn' => 9,
            'endColumn' => 28,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'table' => 
          array (
            'name' => 'table',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 9,
            'endColumn' => 14,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => '\'default\'',
              'attributes' => 
              array (
                'startLine' => 59,
                'endLine' => 59,
                'startTokenPos' => 136,
                'startFilePos' => 1314,
                'endTokenPos' => 136,
                'endFilePos' => 1322,
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
            'startColumn' => 9,
            'endColumn' => 28,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'retryAfter' => 
          array (
            'name' => 'retryAfter',
            'default' => 
            array (
              'code' => '60',
              'attributes' => 
              array (
                'startLine' => 60,
                'endLine' => 60,
                'startTokenPos' => 143,
                'startFilePos' => 1347,
                'endTokenPos' => 143,
                'endFilePos' => 1348,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'dispatchAfterCommit' => 
          array (
            'name' => 'dispatchAfterCommit',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 61,
                'endLine' => 61,
                'startTokenPos' => 150,
                'startFilePos' => 1382,
                'endTokenPos' => 150,
                'endFilePos' => 1386,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 9,
            'endColumn' => 36,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new database queue instance.
 *
 * @param  \\Illuminate\\Database\\Connection  $database
 * @param  string  $table
 * @param  string  $default
 * @param  int  $retryAfter
 * @param  bool  $dispatchAfterCommit
 */',
        'startLine' => 56,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'size' => 
      array (
        'name' => 'size',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 76,
                'endLine' => 76,
                'startTokenPos' => 216,
                'startFilePos' => 1752,
                'endTokenPos' => 216,
                'endFilePos' => 1755,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 76,
            'endLine' => 76,
            'startColumn' => 26,
            'endColumn' => 38,
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
 * Get the size of the queue.
 *
 * @param  string|null  $queue
 * @return int
 */',
        'startLine' => 76,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'pendingSize' => 
      array (
        'name' => 'pendingSize',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 89,
                'endLine' => 89,
                'startTokenPos' => 268,
                'startFilePos' => 2057,
                'endTokenPos' => 268,
                'endFilePos' => 2060,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 33,
            'endColumn' => 45,
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
 * Get the number of pending jobs.
 *
 * @param  string|null  $queue
 * @return int
 */',
        'startLine' => 89,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'delayedSize' => 
      array (
        'name' => 'delayedSize',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 104,
                'endLine' => 104,
                'startTokenPos' => 342,
                'startFilePos' => 2465,
                'endTokenPos' => 342,
                'endFilePos' => 2468,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 104,
            'endLine' => 104,
            'startColumn' => 33,
            'endColumn' => 45,
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
 * Get the number of delayed jobs.
 *
 * @param  string|null  $queue
 * @return int
 */',
        'startLine' => 104,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'reservedSize' => 
      array (
        'name' => 'reservedSize',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 119,
                'endLine' => 119,
                'startTokenPos' => 416,
                'startFilePos' => 2874,
                'endTokenPos' => 416,
                'endFilePos' => 2877,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 119,
            'endLine' => 119,
            'startColumn' => 34,
            'endColumn' => 46,
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
 * Get the number of reserved jobs.
 *
 * @param  string|null  $queue
 * @return int
 */',
        'startLine' => 119,
        'endLine' => 125,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'creationTimeOfOldestPendingJob' => 
      array (
        'name' => 'creationTimeOfOldestPendingJob',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 133,
                'endLine' => 133,
                'startTokenPos' => 474,
                'startFilePos' => 3291,
                'endTokenPos' => 474,
                'endFilePos' => 3294,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 133,
            'endLine' => 133,
            'startColumn' => 52,
            'endColumn' => 64,
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
 * Get the creation timestamp of the oldest pending job, excluding delayed jobs.
 *
 * @param  string|null  $queue
 * @return int|null
 */',
        'startLine' => 133,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'push' => 
      array (
        'name' => 'push',
        'parameters' => 
        array (
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 26,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 151,
                'endLine' => 151,
                'startTokenPos' => 558,
                'startFilePos' => 3805,
                'endTokenPos' => 558,
                'endFilePos' => 3806,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 32,
            'endColumn' => 41,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 151,
                'endLine' => 151,
                'startTokenPos' => 565,
                'startFilePos' => 3818,
                'endTokenPos' => 565,
                'endFilePos' => 3821,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 44,
            'endColumn' => 56,
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
 * Push a new job onto the queue.
 *
 * @param  string  $job
 * @param  mixed  $data
 * @param  string|null  $queue
 * @return mixed
 */',
        'startLine' => 151,
        'endLine' => 162,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'pushRaw' => 
      array (
        'name' => 'pushRaw',
        'parameters' => 
        array (
          'payload' => 
          array (
            'name' => 'payload',
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
            'startColumn' => 29,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 172,
                'endLine' => 172,
                'startTokenPos' => 651,
                'startFilePos' => 4363,
                'endTokenPos' => 651,
                'endFilePos' => 4366,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 39,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 172,
                'endLine' => 172,
                'startTokenPos' => 660,
                'startFilePos' => 4386,
                'endTokenPos' => 661,
                'endFilePos' => 4387,
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
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 54,
            'endColumn' => 72,
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
 * Push a raw payload onto the queue.
 *
 * @param  string  $payload
 * @param  string|null  $queue
 * @param  array  $options
 * @return mixed
 */',
        'startLine' => 172,
        'endLine' => 175,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'later' => 
      array (
        'name' => 'later',
        'parameters' => 
        array (
          'delay' => 
          array (
            'name' => 'delay',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 186,
            'endLine' => 186,
            'startColumn' => 27,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 186,
            'endLine' => 186,
            'startColumn' => 35,
            'endColumn' => 38,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 186,
                'endLine' => 186,
                'startTokenPos' => 699,
                'startFilePos' => 4758,
                'endTokenPos' => 699,
                'endFilePos' => 4759,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 186,
            'endLine' => 186,
            'startColumn' => 41,
            'endColumn' => 50,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 186,
                'endLine' => 186,
                'startTokenPos' => 706,
                'startFilePos' => 4771,
                'endTokenPos' => 706,
                'endFilePos' => 4774,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 186,
            'endLine' => 186,
            'startColumn' => 53,
            'endColumn' => 65,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Push a new job onto the queue after (n) seconds.
 *
 * @param  \\DateTimeInterface|\\DateInterval|int  $delay
 * @param  string  $job
 * @param  mixed  $data
 * @param  string|null  $queue
 * @return mixed
 */',
        'startLine' => 186,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'bulk' => 
      array (
        'name' => 'bulk',
        'parameters' => 
        array (
          'jobs' => 
          array (
            'name' => 'jobs',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 207,
            'endLine' => 207,
            'startColumn' => 26,
            'endColumn' => 30,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 207,
                'endLine' => 207,
                'startTokenPos' => 801,
                'startFilePos' => 5331,
                'endTokenPos' => 801,
                'endFilePos' => 5332,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 207,
            'endLine' => 207,
            'startColumn' => 33,
            'endColumn' => 42,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 207,
                'endLine' => 207,
                'startTokenPos' => 808,
                'startFilePos' => 5344,
                'endTokenPos' => 808,
                'endFilePos' => 5347,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 207,
            'endLine' => 207,
            'startColumn' => 45,
            'endColumn' => 57,
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
 * Push an array of jobs onto the queue.
 *
 * @param  array  $jobs
 * @param  mixed  $data
 * @param  string|null  $queue
 * @return mixed
 */',
        'startLine' => 207,
        'endLine' => 222,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'release' => 
      array (
        'name' => 'release',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 232,
            'endLine' => 232,
            'startColumn' => 29,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 232,
            'endLine' => 232,
            'startColumn' => 37,
            'endColumn' => 40,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'delay' => 
          array (
            'name' => 'delay',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 232,
            'endLine' => 232,
            'startColumn' => 43,
            'endColumn' => 48,
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
 * Release a reserved job back onto the queue after (n) seconds.
 *
 * @param  string  $queue
 * @param  \\Illuminate\\Queue\\Jobs\\DatabaseJobRecord  $job
 * @param  int  $delay
 * @return mixed
 */',
        'startLine' => 232,
        'endLine' => 235,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'pushToDatabase' => 
      array (
        'name' => 'pushToDatabase',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 39,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'payload' => 
          array (
            'name' => 'payload',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 47,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'delay' => 
          array (
            'name' => 'delay',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 246,
                'endLine' => 246,
                'startTokenPos' => 1014,
                'startFilePos' => 6613,
                'endTokenPos' => 1014,
                'endFilePos' => 6613,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 57,
            'endColumn' => 66,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'attempts' => 
          array (
            'name' => 'attempts',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 246,
                'endLine' => 246,
                'startTokenPos' => 1021,
                'startFilePos' => 6628,
                'endTokenPos' => 1021,
                'endFilePos' => 6628,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 69,
            'endColumn' => 81,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Push a raw payload to the database with a given delay of (n) seconds.
 *
 * @param  string|null  $queue
 * @param  string  $payload
 * @param  \\DateTimeInterface|\\DateInterval|int  $delay
 * @param  int  $attempts
 * @return mixed
 */',
        'startLine' => 246,
        'endLine' => 254,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'buildDatabaseRecord' => 
      array (
        'name' => 'buildDatabaseRecord',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 265,
            'endLine' => 265,
            'startColumn' => 44,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'payload' => 
          array (
            'name' => 'payload',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 265,
            'endLine' => 265,
            'startColumn' => 52,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'availableAt' => 
          array (
            'name' => 'availableAt',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 265,
            'endLine' => 265,
            'startColumn' => 62,
            'endColumn' => 73,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'attempts' => 
          array (
            'name' => 'attempts',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 265,
                'endLine' => 265,
                'startTokenPos' => 1094,
                'startFilePos' => 7183,
                'endTokenPos' => 1094,
                'endFilePos' => 7183,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 265,
            'endLine' => 265,
            'startColumn' => 76,
            'endColumn' => 88,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create an array to insert for the given job.
 *
 * @param  string|null  $queue
 * @param  string  $payload
 * @param  int  $availableAt
 * @param  int  $attempts
 * @return array
 */',
        'startLine' => 265,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'pop' => 
      array (
        'name' => 'pop',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 285,
                'endLine' => 285,
                'startTokenPos' => 1166,
                'startFilePos' => 7677,
                'endTokenPos' => 1166,
                'endFilePos' => 7680,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 285,
            'endLine' => 285,
            'startColumn' => 25,
            'endColumn' => 37,
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
 * Pop the next job off of the queue.
 *
 * @param  string|null  $queue
 * @return \\Illuminate\\Contracts\\Queue\\Job|null
 *
 * @throws \\Throwable
 */',
        'startLine' => 285,
        'endLine' => 311,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'getNextAvailableJob' => 
      array (
        'name' => 'getNextAvailableJob',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 319,
            'endLine' => 319,
            'startColumn' => 44,
            'endColumn' => 49,
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
 * Get the next available job for the queue.
 *
 * @param  string|null  $queue
 * @return \\Illuminate\\Queue\\Jobs\\DatabaseJobRecord|null
 */',
        'startLine' => 319,
        'endLine' => 332,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'getLockForPopping' => 
      array (
        'name' => 'getLockForPopping',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the lock required for popping the next job.
 *
 * @return string|bool
 */',
        'startLine' => 339,
        'endLine' => 365,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'isAvailable' => 
      array (
        'name' => 'isAvailable',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 373,
            'endLine' => 373,
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
 * Modify the query to check for available jobs.
 *
 * @param  \\Illuminate\\Database\\Query\\Builder  $query
 * @return void
 */',
        'startLine' => 373,
        'endLine' => 379,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'isReservedButExpired' => 
      array (
        'name' => 'isReservedButExpired',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 387,
            'endLine' => 387,
            'startColumn' => 45,
            'endColumn' => 50,
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
 * Modify the query to check for jobs that are reserved but have expired.
 *
 * @param  \\Illuminate\\Database\\Query\\Builder  $query
 * @return void
 */',
        'startLine' => 387,
        'endLine' => 394,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'marshalJob' => 
      array (
        'name' => 'marshalJob',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 403,
            'endLine' => 403,
            'startColumn' => 35,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 403,
            'endLine' => 403,
            'startColumn' => 43,
            'endColumn' => 46,
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
 * Marshal the reserved job into a DatabaseJob instance.
 *
 * @param  string  $queue
 * @param  \\Illuminate\\Queue\\Jobs\\DatabaseJobRecord  $job
 * @return \\Illuminate\\Queue\\Jobs\\DatabaseJob
 */',
        'startLine' => 403,
        'endLine' => 412,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'markJobAsReserved' => 
      array (
        'name' => 'markJobAsReserved',
        'parameters' => 
        array (
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 420,
            'endLine' => 420,
            'startColumn' => 42,
            'endColumn' => 45,
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
 * Mark the given job ID as reserved.
 *
 * @param  \\Illuminate\\Queue\\Jobs\\DatabaseJobRecord  $job
 * @return \\Illuminate\\Queue\\Jobs\\DatabaseJobRecord
 */',
        'startLine' => 420,
        'endLine' => 428,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'deleteReserved' => 
      array (
        'name' => 'deleteReserved',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 439,
            'endLine' => 439,
            'startColumn' => 36,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 439,
            'endLine' => 439,
            'startColumn' => 44,
            'endColumn' => 46,
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
 * Delete a reserved job from the queue.
 *
 * @param  string  $queue
 * @param  string  $id
 * @return void
 *
 * @throws \\Throwable
 */',
        'startLine' => 439,
        'endLine' => 446,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'deleteAndRelease' => 
      array (
        'name' => 'deleteAndRelease',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 456,
            'endLine' => 456,
            'startColumn' => 38,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 456,
            'endLine' => 456,
            'startColumn' => 46,
            'endColumn' => 49,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'delay' => 
          array (
            'name' => 'delay',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 456,
            'endLine' => 456,
            'startColumn' => 52,
            'endColumn' => 57,
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
 * Delete a reserved job from the reserved queue and release it.
 *
 * @param  string  $queue
 * @param  \\Illuminate\\Queue\\Jobs\\DatabaseJob  $job
 * @param  int  $delay
 * @return void
 */',
        'startLine' => 456,
        'endLine' => 465,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'clear' => 
      array (
        'name' => 'clear',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 473,
            'endLine' => 473,
            'startColumn' => 27,
            'endColumn' => 32,
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
 * Delete all of the jobs from the queue.
 *
 * @param  string  $queue
 * @return int
 */',
        'startLine' => 473,
        'endLine' => 478,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'getQueue' => 
      array (
        'name' => 'getQueue',
        'parameters' => 
        array (
          'queue' => 
          array (
            'name' => 'queue',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 486,
            'endLine' => 486,
            'startColumn' => 30,
            'endColumn' => 35,
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
 * Get the queue or return the default.
 *
 * @param  string|null  $queue
 * @return string
 */',
        'startLine' => 486,
        'endLine' => 489,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'aliasName' => NULL,
      ),
      'getDatabase' => 
      array (
        'name' => 'getDatabase',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the underlying database instance.
 *
 * @return \\Illuminate\\Database\\Connection
 */',
        'startLine' => 496,
        'endLine' => 499,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue',
        'declaringClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'implementingClassName' => 'Illuminate\\Queue\\DatabaseQueue',
        'currentClassName' => 'Illuminate\\Queue\\DatabaseQueue',
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