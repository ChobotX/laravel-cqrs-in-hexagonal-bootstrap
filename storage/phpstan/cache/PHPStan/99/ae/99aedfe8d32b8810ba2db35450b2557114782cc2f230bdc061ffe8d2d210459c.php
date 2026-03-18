<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Command/RevokeRecordShare/RevokeRecordShareCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Command\RevokeRecordShare\RevokeRecordShareCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-705d41484f1b26d06af4b9742c6c9270aa9339d1941c27ca77a6048f5fb7be39',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
        'filename' => '/var/www/html/app/Domain/Authorization/Command/RevokeRecordShare/RevokeRecordShareCommand.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare',
    'name' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
    'shortName' => 'RevokeRecordShareCommand',
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
          'reason' => 
          array (
            'code' => '\'Record share revocation is enforced per-resource by the handler\'',
            'attributes' => 
            array (
              'startLine' => 10,
              'endLine' => 10,
              'startTokenPos' => 31,
              'startFilePos' => 216,
              'endTokenPos' => 31,
              'endFilePos' => 280,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 10,
    'endLine' => 18,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'App\\Contract\\Command\\Command',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'granteeUserId' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
        'name' => 'granteeUserId',
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
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 9,
        'endColumn' => 36,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'resourceType' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
        'name' => 'resourceType',
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
        'startLine' => 15,
        'endLine' => 15,
        'startColumn' => 9,
        'endColumn' => 35,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'resourceId' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
        'name' => 'resourceId',
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
        'endColumn' => 33,
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
          'granteeUserId' => 
          array (
            'name' => 'granteeUserId',
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
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 9,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'resourceType' => 
          array (
            'name' => 'resourceType',
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
            'startLine' => 15,
            'endLine' => 15,
            'startColumn' => 9,
            'endColumn' => 35,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'resourceId' => 
          array (
            'name' => 'resourceId',
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
            'endColumn' => 33,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 13,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare',
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
        'currentClassName' => 'App\\Domain\\Authorization\\Command\\RevokeRecordShare\\RevokeRecordShareCommand',
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