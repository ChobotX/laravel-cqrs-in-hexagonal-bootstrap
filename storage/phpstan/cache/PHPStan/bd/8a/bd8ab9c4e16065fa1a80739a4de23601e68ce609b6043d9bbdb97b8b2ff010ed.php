<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/User/Exception/EmailAlreadyExistsException.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\User\Exception\EmailAlreadyExistsException
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-78f327d27674e3b7e209160256db4b962ee0291b4478af57d6eedff9359888c8',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
        'filename' => '/var/www/html/app/Domain/User/Exception/EmailAlreadyExistsException.php',
      ),
    ),
    'namespace' => 'App\\Domain\\User\\Exception',
    'name' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
    'shortName' => 'EmailAlreadyExistsException',
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
    'endLine' => 27,
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
      'email' => 
      array (
        'declaringClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
        'implementingClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
        'name' => 'email',
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
        'startLine' => 13,
        'endLine' => 13,
        'startColumn' => 33,
        'endColumn' => 61,
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
          'email' => 
          array (
            'name' => 'email',
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
            'startLine' => 13,
            'endLine' => 13,
            'startColumn' => 33,
            'endColumn' => 61,
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
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\User\\Exception',
        'declaringClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
        'implementingClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
        'currentClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
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
            'startLine' => 18,
            'endLine' => 18,
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
        'startLine' => 18,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\User\\Exception',
        'declaringClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
        'implementingClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
        'currentClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
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
        'startLine' => 23,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\User\\Exception',
        'declaringClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
        'implementingClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
        'currentClassName' => 'App\\Domain\\User\\Exception\\EmailAlreadyExistsException',
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