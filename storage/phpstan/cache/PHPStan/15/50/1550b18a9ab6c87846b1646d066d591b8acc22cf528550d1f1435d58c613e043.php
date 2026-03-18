<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Presentation/Http/Controller/Web/User/ShowEditUserController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Presentation\Http\Controller\Web\User\ShowEditUserController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-6641862a330a97a95c150cea1b1c522f60af04e6bc13f3e1307d324f2d6dd44d',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'filename' => '/var/www/html/app/Presentation/Http/Controller/Web/User/ShowEditUserController.php',
      ),
    ),
    'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
    'name' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
    'shortName' => 'ShowEditUserController',
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
            'code' => '\'users.list.update\'',
            'attributes' => 
            array (
              'startLine' => 20,
              'endLine' => 20,
              'startTokenPos' => 78,
              'startFilePos' => 727,
              'endTokenPos' => 78,
              'endFilePos' => 745,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 20,
    'endLine' => 70,
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
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
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
        'startLine' => 24,
        'endLine' => 24,
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
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
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
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 9,
        'endColumn' => 56,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'authenticatedUser' => 
      array (
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'name' => 'authenticatedUser',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Contract\\Auth\\AuthenticatedUser',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 9,
        'endColumn' => 52,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'authorizationChecker' => 
      array (
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'name' => 'authorizationChecker',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Contract\\Authorization\\AuthorizationChecker',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 9,
        'endColumn' => 58,
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
            'startLine' => 24,
            'endLine' => 24,
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
            'startLine' => 25,
            'endLine' => 25,
            'startColumn' => 9,
            'endColumn' => 56,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'authenticatedUser' => 
          array (
            'name' => 'authenticatedUser',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Contract\\Auth\\AuthenticatedUser',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 9,
            'endColumn' => 52,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'authorizationChecker' => 
          array (
            'name' => 'authorizationChecker',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Contract\\Authorization\\AuthorizationChecker',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 27,
            'endLine' => 27,
            'startColumn' => 9,
            'endColumn' => 58,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 23,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
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
            'startLine' => 30,
            'endLine' => 30,
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
            'name' => 'Illuminate\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 30,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'aliasName' => NULL,
      ),
      'availableModules' => 
      array (
        'name' => 'availableModules',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @return array<string, array{features: array<string, array{actions: list<string>}>}> */',
        'startLine' => 63,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\Web\\User\\ShowEditUserController',
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