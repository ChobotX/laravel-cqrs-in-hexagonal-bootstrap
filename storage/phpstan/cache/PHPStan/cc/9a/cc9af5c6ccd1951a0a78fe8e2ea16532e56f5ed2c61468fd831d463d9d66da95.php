<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Query/GetActiveImpersonation/GetActiveImpersonationQuery.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Query\GetActiveImpersonation\GetActiveImpersonationQuery
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-b1145c15f2ddb263b9b444f325929c79d436b17a8ad27c1c8a42cb238a01a5fe',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Query\\GetActiveImpersonation\\GetActiveImpersonationQuery',
        'filename' => '/var/www/html/app/Domain/Authorization/Query/GetActiveImpersonation/GetActiveImpersonationQuery.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Query\\GetActiveImpersonation',
    'name' => 'App\\Domain\\Authorization\\Query\\GetActiveImpersonation\\GetActiveImpersonationQuery',
    'shortName' => 'GetActiveImpersonationQuery',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements Query<array{impersonator_id: string, impersonated_user_id: string}|null> */',
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
            'code' => '\'Used internally for impersonation state checks\'',
            'attributes' => 
            array (
              'startLine' => 11,
              'endLine' => 11,
              'startTokenPos' => 33,
              'startFilePos' => 307,
              'endTokenPos' => 33,
              'endFilePos' => 354,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 11,
    'endLine' => 17,
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
      'impersonatorId' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetActiveImpersonation\\GetActiveImpersonationQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetActiveImpersonation\\GetActiveImpersonationQuery',
        'name' => 'impersonatorId',
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
          'impersonatorId' => 
          array (
            'name' => 'impersonatorId',
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
        'startLine' => 14,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetActiveImpersonation',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetActiveImpersonation\\GetActiveImpersonationQuery',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetActiveImpersonation\\GetActiveImpersonationQuery',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetActiveImpersonation\\GetActiveImpersonationQuery',
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