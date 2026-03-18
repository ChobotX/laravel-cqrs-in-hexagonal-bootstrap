<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Infrastructure/Eloquent/Authorization/RoleModel.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Infrastructure\Eloquent\Authorization\RoleModel
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-142c0a893dd3516ee4a48cd00fe92fcc49a178955483305b20a3751be414616c',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'filename' => '/var/www/html/app/Infrastructure/Eloquent/Authorization/RoleModel.php',
      ),
    ),
    'namespace' => 'App\\Infrastructure\\Eloquent\\Authorization',
    'name' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
    'shortName' => 'RoleModel',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/** @use HasFactory<RoleModelFactory> */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 45,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasUuids',
      2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'incrementing' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'name' => 'incrementing',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 80,
            'startFilePos' => 540,
            'endTokenPos' => 80,
            'endFilePos' => 544,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'table' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'roles\'',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 89,
            'startFilePos' => 571,
            'endTokenPos' => 89,
            'endFilePos' => 577,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'id\', \'organization_id\', \'name\', \'description\', \'is_system\']',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 98,
            'startFilePos' => 607,
            'endTokenPos' => 112,
            'endFilePos' => 667,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 88,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'keyType' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'name' => 'keyType',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'string\'',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 121,
            'startFilePos' => 696,
            'endTokenPos' => 121,
            'endFilePos' => 703,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'newFactory' => 
      array (
        'name' => 'newFactory',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Database\\Factories\\RoleModelFactory',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 29,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'App\\Infrastructure\\Eloquent\\Authorization',
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'currentClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'aliasName' => NULL,
      ),
      'permissions' => 
      array (
        'name' => 'permissions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @return HasMany<RolePermissionModel, $this> */',
        'startLine' => 35,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Infrastructure\\Eloquent\\Authorization',
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'currentClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'aliasName' => NULL,
      ),
      'userRoles' => 
      array (
        'name' => 'userRoles',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @return HasMany<UserRoleModel, $this> */',
        'startLine' => 41,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Infrastructure\\Eloquent\\Authorization',
        'declaringClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'implementingClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
        'currentClassName' => 'App\\Infrastructure\\Eloquent\\Authorization\\RoleModel',
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