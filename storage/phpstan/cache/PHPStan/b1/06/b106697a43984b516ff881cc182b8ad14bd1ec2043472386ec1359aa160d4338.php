<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Query/GetUserOverrides/GetUserOverridesHandler.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Query\GetUserOverrides\GetUserOverridesHandler
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-8ce369aed54f8725acdce5898230a646f94f06fd470a97b58f926dc92bf00997',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
        'filename' => '/var/www/html/app/Domain/Authorization/Query/GetUserOverrides/GetUserOverridesHandler.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides',
    'name' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
    'shortName' => 'GetUserOverridesHandler',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements QueryHandler<GetUserOverridesQuery, list<UserPermissionOverride>> */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 24,
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
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
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
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 9,
        'endColumn' => 66,
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
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 9,
            'endColumn' => 66,
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
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
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
            'startLine' => 20,
            'endLine' => 20,
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
        'docComment' => '/** @return list<UserPermissionOverride> */',
        'startLine' => 20,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetUserOverrides\\GetUserOverridesHandler',
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