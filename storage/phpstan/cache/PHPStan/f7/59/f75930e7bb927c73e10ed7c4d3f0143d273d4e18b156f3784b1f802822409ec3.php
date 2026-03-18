<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Query/ListRoles/ListRolesQuery.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Query\ListRoles\ListRolesQuery
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-11d49b7b86510d377cdce26f68689a5d3ca49cc93ea904feb3fe988375e1658f',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
        'filename' => '/var/www/html/app/Domain/Authorization/Query/ListRoles/ListRolesQuery.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Query\\ListRoles',
    'name' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
    'shortName' => 'ListRolesQuery',
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
              'startFilePos' => 264,
              'endTokenPos' => 35,
              'endFilePos' => 281,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 12,
    'endLine' => 18,
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
      'organizationId' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
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
        'startLine' => 16,
        'endLine' => 16,
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
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 9,
            'endColumn' => 37,
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
        'namespace' => 'App\\Domain\\Authorization\\Query\\ListRoles',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
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