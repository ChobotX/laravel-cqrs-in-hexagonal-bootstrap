<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Presentation/Http/Controller/Web/User/CreateUserController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Presentation\Http\Controller\Web\User\CreateUserController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-060eb68923598bb58d48ab309d36694fabd0ca1e8bba5ad19dbd71112112115a',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
        'filename' => '/var/www/html/app/Presentation/Http/Controller/Web/User/CreateUserController.php',
      ),
    ),
    'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
    'name' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
    'shortName' => 'CreateUserController',
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
        'name' => 'App\\Application\\Authorization\\RequiresPermission',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '\'users.list.create\'',
            'attributes' => 
            array (
              'startLine' => 13,
              'endLine' => 13,
              'startTokenPos' => 43,
              'startFilePos' => 359,
              'endTokenPos' => 43,
              'endFilePos' => 377,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 13,
    'endLine' => 29,
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
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
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
        'startLine' => 17,
        'endLine' => 17,
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
            'startLine' => 17,
            'endLine' => 17,
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
        'startLine' => 16,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
        'aliasName' => NULL,
      ),
      '__invoke' => 
      array (
        'name' => '__invoke',
        'parameters' => 
        array (
          'createUserRequest' => 
          array (
            'name' => 'createUserRequest',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Presentation\\Http\\Request\\Web\\User\\CreateUserRequest',
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
            'name' => 'Illuminate\\Http\\RedirectResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 20,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\CreateUserController',
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