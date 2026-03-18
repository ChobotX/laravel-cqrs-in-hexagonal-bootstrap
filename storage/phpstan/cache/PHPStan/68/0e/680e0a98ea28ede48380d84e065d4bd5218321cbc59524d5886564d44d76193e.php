<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Query/GetEffectivePermissions/GetEffectivePermissionsQuery.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-2b0c63d9014ddd10176cf3208a06fa5c5f014d87bd53d4c0c0481e70632d0dff',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
        'filename' => '/var/www/html/app/Domain/Authorization/Query/GetEffectivePermissions/GetEffectivePermissionsQuery.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions',
    'name' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
    'shortName' => 'GetEffectivePermissionsQuery',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements Query<list<EffectivePermission>> */',
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
              'startFilePos' => 308,
              'endTokenPos' => 35,
              'endFilePos' => 325,
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
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
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
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
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
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
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