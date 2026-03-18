<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Presentation/Http/Controller/Web/Authorization/ShowEditRoleController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Presentation\Http\Controller\Web\Authorization\ShowEditRoleController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-fe5fbdc9753d2d7232b0aa1c459cc4efc2422172b623dfc6f995d1921126956d',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
        'filename' => '/var/www/html/app/Presentation/Http/Controller/Web/Authorization/ShowEditRoleController.php',
      ),
    ),
    'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization',
    'name' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
    'shortName' => 'ShowEditRoleController',
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
            'code' => '\'users.roles.update\'',
            'attributes' => 
            array (
              'startLine' => 13,
              'endLine' => 13,
              'startTokenPos' => 43,
              'startFilePos' => 378,
              'endTokenPos' => 43,
              'endFilePos' => 397,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 13,
    'endLine' => 27,
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
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
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
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 9,
        'endColumn' => 34,
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
            'startLine' => 17,
            'endLine' => 17,
            'startColumn' => 9,
            'endColumn' => 34,
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
        'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
        'aliasName' => NULL,
      ),
      '__invoke' => 
      array (
        'name' => '__invoke',
        'parameters' => 
        array (
          'roleId' => 
          array (
            'name' => 'roleId',
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
            'startLine' => 20,
            'endLine' => 20,
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
        'startLine' => 20,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization',
        'declaringClassName' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
        'implementingClassName' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
        'currentClassName' => 'App\\Presentation\\Http\\Controller\\Web\\Authorization\\ShowEditRoleController',
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