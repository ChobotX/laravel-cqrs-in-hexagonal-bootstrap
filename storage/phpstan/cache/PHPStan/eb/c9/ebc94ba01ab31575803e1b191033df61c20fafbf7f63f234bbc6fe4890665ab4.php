<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Command/StopImpersonation/StopImpersonationCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Command\StopImpersonation\StopImpersonationCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-335c134b47ab4baf903c8a931ed62e71fb1a2e22dfa7f044a61040b6ec34ef72',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Command\\StopImpersonation\\StopImpersonationCommand',
        'filename' => '/var/www/html/app/Domain/Authorization/Command/StopImpersonation/StopImpersonationCommand.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Command\\StopImpersonation',
    'name' => 'App\\Domain\\Authorization\\Command\\StopImpersonation\\StopImpersonationCommand',
    'shortName' => 'StopImpersonationCommand',
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
            'code' => '\'Users must always be able to stop their own impersonation\'',
            'attributes' => 
            array (
              'startLine' => 10,
              'endLine' => 10,
              'startTokenPos' => 31,
              'startFilePos' => 216,
              'endTokenPos' => 31,
              'endFilePos' => 274,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 10,
    'endLine' => 16,
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
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\StopImpersonation\\StopImpersonationCommand',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\StopImpersonation\\StopImpersonationCommand',
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
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 13,
        'endLine' => 15,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Command\\StopImpersonation',
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\StopImpersonation\\StopImpersonationCommand',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\StopImpersonation\\StopImpersonationCommand',
        'currentClassName' => 'App\\Domain\\Authorization\\Command\\StopImpersonation\\StopImpersonationCommand',
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