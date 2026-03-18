<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Presentation/Http/Controller/Authorization/GetUserPermissionsController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Presentation\Http\Controller\Authorization\GetUserPermissionsController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-d490454c37f6a7dbbd618892da624ada37b7486f2ca3b7440c73b8be6cd29188',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'filename' => '/var/www/html/app/Presentation/Http/Controller/Authorization/GetUserPermissionsController.php',
      ),
    ),
    'namespace' => 'App\\Presentation\\Http\\Controller\\Authorization',
    'name' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
    'shortName' => 'GetUserPermissionsController',
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
              'startLine' => 15,
              'endLine' => 15,
              'startTokenPos' => 53,
              'startFilePos' => 483,
              'endTokenPos' => 53,
              'endFilePos' => 524,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 15,
    'endLine' => 46,
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
      'queryBus' => 
      array (
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'name' => 'queryBus',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Application\\Bus\\QueryBus',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 9,
        'endColumn' => 34,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'organizationContext' => 
      array (
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'name' => 'organizationContext',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Contract\\Organization\\OrganizationContext',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 9,
        'endColumn' => 56,
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
          'queryBus' => 
          array (
            'name' => 'queryBus',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Application\\Bus\\QueryBus',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 19,
            'endLine' => 19,
            'startColumn' => 9,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'organizationContext' => 
          array (
            'name' => 'organizationContext',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Contract\\Organization\\OrganizationContext',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 20,
            'endLine' => 20,
            'startColumn' => 9,
            'endColumn' => 56,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 18,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Presentation\\Http\\Controller\\Authorization',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'aliasName' => NULL,
      ),
      '__invoke' => 
      array (
        'name' => '__invoke',
        'parameters' => 
        array (
          'userId' => 
          array (
            'name' => 'userId',
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
            'startLine' => 23,
            'endLine' => 23,
            'startColumn' => 30,
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
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 23,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Presentation\\Http\\Controller\\Authorization',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\Authorization\\GetUserPermissionsController',
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