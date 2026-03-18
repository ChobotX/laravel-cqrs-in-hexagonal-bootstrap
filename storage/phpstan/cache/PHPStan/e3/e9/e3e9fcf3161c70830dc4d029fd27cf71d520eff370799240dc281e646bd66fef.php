<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Exception/DuplicateRoleAssignmentException.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Exception\DuplicateRoleAssignmentException
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-19d4187ca1c88d7557e94d1d6af5ef0160685370bc5da38b8d90463584cb84f3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'filename' => '/var/www/html/app/Domain/Authorization/Exception/DuplicateRoleAssignmentException.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Exception',
    'name' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
    'shortName' => 'DuplicateRoleAssignmentException',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 29,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'RuntimeException',
    'implementsClassNames' => 
    array (
      0 => 'App\\Contract\\Exception\\DomainException',
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
        'declaringClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'implementingClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'name' => 'userId',
        'modifiers' => 2177,
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
        'endColumn' => 38,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'roleId' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'implementingClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'name' => 'roleId',
        'modifiers' => 2177,
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
        'endColumn' => 38,
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
            'startLine' => 14,
            'endLine' => 14,
            'startColumn' => 9,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'roleId' => 
          array (
            'name' => 'roleId',
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
            'endColumn' => 38,
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
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Exception',
        'declaringClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'implementingClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'currentClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'aliasName' => NULL,
      ),
      'userMessage' => 
      array (
        'name' => 'userMessage',
        'parameters' => 
        array (
          'translator' => 
          array (
            'name' => 'translator',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Contract\\Translation\\Translator',
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
            'startColumn' => 33,
            'endColumn' => 54,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 20,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Exception',
        'declaringClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'implementingClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'currentClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'aliasName' => NULL,
      ),
      'statusCode' => 
      array (
        'name' => 'statusCode',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 25,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Exception',
        'declaringClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'implementingClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
        'currentClassName' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
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