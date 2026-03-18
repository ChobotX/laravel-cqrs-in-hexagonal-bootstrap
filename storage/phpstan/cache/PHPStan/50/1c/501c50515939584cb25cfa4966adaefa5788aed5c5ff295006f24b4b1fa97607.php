<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Presentation/Http/Controller/User/UpdateUserController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Presentation\Http\Controller\User\UpdateUserController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-807881ace3464d48cb55b6d26e0d9d592c99018aa195fbf09e5ffb2533610f93',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
        'filename' => '/var/www/html/app/Presentation/Http/Controller/User/UpdateUserController.php',
      ),
    ),
    'namespace' => 'App\\Presentation\\Http\\Controller\\User',
    'name' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
    'shortName' => 'UpdateUserController',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => NULL,
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'App\\Application\\Authorization\\SkipPermissionCheck',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '\'Permission enforced by command/query bus\'',
            'attributes' => 
            array (
              'startLine' => 12,
              'endLine' => 12,
              'startTokenPos' => 38,
              'startFilePos' => 285,
              'endTokenPos' => 38,
              'endFilePos' => 326,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 12,
    'endLine' => 25,
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
      'commandBus' => 
      array (
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
        'name' => 'commandBus',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Application\\Bus\\CommandBus',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 9,
        'endColumn' => 38,
        'isPromoted' => true,
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
          'commandBus' => 
          array (
            'name' => 'commandBus',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Application\\Bus\\CommandBus',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 9,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 15,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Presentation\\Http\\Controller\\User',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
        'aliasName' => NULL,
      ),
      '__invoke' => 
      array (
        'name' => '__invoke',
        'parameters' => 
        array (
          'updateUserRequest' => 
          array (
            'name' => 'updateUserRequest',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Presentation\\Http\\Request\\User\\UpdateUserRequest',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 19,
            'endLine' => 19,
            'startColumn' => 30,
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
            'name' => 'Illuminate\\Http\\Response',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 19,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Presentation\\Http\\Controller\\User',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\User\\UpdateUserController',
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