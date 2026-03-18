<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Query/GetEffectivePermissions/GetEffectivePermissionsHandler.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsHandler
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-61084dc46d8ffdf72db3e80a37d3e847b7841871dfc52c32f7e135686a5a7e26',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'filename' => '/var/www/html/app/Domain/Authorization/Query/GetEffectivePermissions/GetEffectivePermissionsHandler.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions',
    'name' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
    'shortName' => 'GetEffectivePermissionsHandler',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements QueryHandler<GetEffectivePermissionsQuery, list<EffectivePermission>> */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 33,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'App\\Contract\\Query\\QueryHandler',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'userPermissionRepository' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'name' => 'userPermissionRepository',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Domain\\Authorization\\UserPermissionRepository',
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
        'endColumn' => 66,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'permissionResolver' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'name' => 'permissionResolver',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Domain\\Authorization\\PermissionResolver',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 9,
        'endColumn' => 54,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'availableModules' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'name' => 'availableModules',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 9,
        'endColumn' => 39,
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
          'userPermissionRepository' => 
          array (
            'name' => 'userPermissionRepository',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Domain\\Authorization\\UserPermissionRepository',
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
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'permissionResolver' => 
          array (
            'name' => 'permissionResolver',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Domain\\Authorization\\PermissionResolver',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 9,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'availableModules' => 
          array (
            'name' => 'availableModules',
            'default' => NULL,
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
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 9,
            'endColumn' => 39,
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
 * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
 */',
        'startLine' => 19,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Contract\\Query\\Query',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 28,
            'endColumn' => 39,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @return list<EffectivePermission> */',
        'startLine' => 26,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsHandler',
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