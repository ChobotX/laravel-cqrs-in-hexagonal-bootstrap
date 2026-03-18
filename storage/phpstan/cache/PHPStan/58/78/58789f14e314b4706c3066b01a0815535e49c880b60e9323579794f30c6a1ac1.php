<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Command/StartImpersonation/StartImpersonationCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Command\StartImpersonation\StartImpersonationCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-b3ec1d250cbb4f8b2cb49855715ba049cd98e12fb8b5013ef7f09f347260ee57',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Command\\StartImpersonation\\StartImpersonationCommand',
        'filename' => '/var/www/html/app/Domain/Authorization/Command/StartImpersonation/StartImpersonationCommand.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Command\\StartImpersonation',
    'name' => 'App\\Domain\\Authorization\\Command\\StartImpersonation\\StartImpersonationCommand',
    'shortName' => 'StartImpersonationCommand',
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
            'code' => '\'Handler enforces super-admin check internally\'',
            'attributes' => 
            array (
              'startLine' => 10,
              'endLine' => 10,
              'startTokenPos' => 31,
              'startFilePos' => 217,
              'endTokenPos' => 31,
              'endFilePos' => 263,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 10,
    'endLine' => 17,
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
      'impersonatorId' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\StartImpersonation\\StartImpersonationCommand',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\StartImpersonation\\StartImpersonationCommand',
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
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 9,
        'endColumn' => 37,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'targetUserId' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\StartImpersonation\\StartImpersonationCommand',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\StartImpersonation\\StartImpersonationCommand',
        'name' => 'targetUserId',
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
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 9,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'targetUserId' => 
          array (
            'name' => 'targetUserId',
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
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 13,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Command\\StartImpersonation',
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\StartImpersonation\\StartImpersonationCommand',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\StartImpersonation\\StartImpersonationCommand',
        'currentClassName' => 'App\\Domain\\Authorization\\Command\\StartImpersonation\\StartImpersonationCommand',
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