<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Query/GetUserRoles/GetUserRolesQuery.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-e4f2f8d7d8254b519ec5ae0b7b9451e12eb2d6f7dc04a89c86afe24fe370083d',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
        'filename' => '/var/www/html/app/Domain/Authorization/Query/GetUserRoles/GetUserRolesQuery.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Query\\GetUserRoles',
    'name' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
    'shortName' => 'GetUserRolesQuery',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements Query<list<Role>> */',
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
            'code' => '\'users.roles.read\'',
            'attributes' => 
            array (
              'startLine' => 12,
              'endLine' => 12,
              'startTokenPos' => 35,
              'startFilePos' => 267,
              'endTokenPos' => 35,
              'endFilePos' => 284,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 12,
    'endLine' => 19,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'App\\Contract\\Query\\Query',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'userId' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
        'name' => 'userId',
        'modifiers' => 1,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
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
        'endColumn' => 29,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'organizationId' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
        'name' => 'organizationId',
        'modifiers' => 1,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
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
        'endColumn' => 37,
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
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'organizationId' => 
          array (
            'name' => 'organizationId',
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
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 17,
            'endLine' => 17,
            'startColumn' => 9,
            'endColumn' => 37,
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
        'startLine' => 15,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetUserRoles',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
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