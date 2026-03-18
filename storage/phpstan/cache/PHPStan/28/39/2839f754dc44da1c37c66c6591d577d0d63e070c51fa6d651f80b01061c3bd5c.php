<?php declare(strict_types = 1);

// odsl-/var/www/html/tests/Unit/Domain/Authorization/PermissionResolverTest.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-findPermission
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-496eb58e4dcd3da8398e3b0e6932c741c371aae4f39c76d5c3de142d7f32e29a',
   'data' => 
  array (
    'name' => 'findPermission',
    'parameters' => 
    array (
      'permissions' => 
      array (
        'name' => 'permissions',
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
        'isPromoted' => false,
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 25,
        'endColumn' => 42,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'key' => 
      array (
        'name' => 'key',
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
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 45,
        'endColumn' => 55,
        'parameterIndex' => 1,
        'isOptional' => false,
      ),
    ),
    'returnsReference' => false,
    'returnType' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
      'data' => 
      array (
        'types' => 
        array (
          0 => 
          array (
            'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
            'data' => 
            array (
              'name' => 'App\\Domain\\Authorization\\EffectivePermission',
              'isIdentifier' => false,
            ),
          ),
          1 => 
          array (
            'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
            'data' => 
            array (
              'name' => 'null',
              'isIdentifier' => true,
            ),
          ),
        ),
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/** @param list<EffectivePermission> $permissions */',
    'startLine' => 79,
    'endLine' => 88,
    'startColumn' => 1,
    'endColumn' => 1,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'findPermission',
        'filename' => '/var/www/html/tests/Unit/Domain/Authorization/PermissionResolverTest.php',
      ),
    ),
  ),
));