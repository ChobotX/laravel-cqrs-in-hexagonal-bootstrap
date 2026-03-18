<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Infrastructure/Eloquent/User/UserModel.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Infrastructure\Eloquent\User\UserModel
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-c87dffa035d06f7f9d272b65f8c864dd12685f497cdc588e356b4b34d3f246b0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'filename' => '/var/www/html/app/Infrastructure/Eloquent/User/UserModel.php',
      ),
    ),
    'namespace' => 'App\\Infrastructure\\Eloquent\\User',
    'name' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
    'shortName' => 'UserModel',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/** @use HasFactory<UserModelFactory> */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 37,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Foundation\\Auth\\User',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Laravel\\Sanctum\\HasApiTokens',
      1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      2 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasUuids',
      3 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'incrementing' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'name' => 'incrementing',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 89,
            'startFilePos' => 561,
            'endTokenPos' => 89,
            'endFilePos' => 565,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'table' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'users\'',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 98,
            'startFilePos' => 592,
            'endTokenPos' => 98,
            'endFilePos' => 598,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'id\', \'name\', \'email\', \'password\']',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 107,
            'startFilePos' => 628,
            'endTokenPos' => 118,
            'endFilePos' => 662,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 62,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'keyType' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'name' => 'keyType',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'string\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 127,
            'startFilePos' => 691,
            'endTokenPos' => 127,
            'endFilePos' => 698,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'hidden' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'name' => 'hidden',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'password\']',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 138,
            'startFilePos' => 755,
            'endTokenPos' => 140,
            'endFilePos' => 766,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 37,
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
      'newFactory' => 
      array (
        'name' => 'newFactory',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Database\\Factories\\UserModelFactory',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 33,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'App\\Infrastructure\\Eloquent\\User',
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
        'currentClassName' => 'App\\Infrastructure\\Eloquent\\User\\UserModel',
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