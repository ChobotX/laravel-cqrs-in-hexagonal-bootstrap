<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Infrastructure/Authorization/EventHandler/InvalidateCacheOnRoleAssigned.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleAssigned
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-0370ab5e64257e6cfdd4fa7f4f724e4f167def4baded47932c2c96221d3b9a01',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
        'filename' => '/var/www/html/app/Infrastructure/Authorization/EventHandler/InvalidateCacheOnRoleAssigned.php',
      ),
    ),
    'namespace' => 'App\\Infrastructure\\Authorization\\EventHandler',
    'name' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
    'shortName' => 'InvalidateCacheOnRoleAssigned',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements DomainEventHandler<RoleAssignedToUser> */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 23,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'App\\Contract\\Event\\DomainEventHandler',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'cacheRepository' => 
      array (
        'declaringClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
        'implementingClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
        'name' => 'cacheRepository',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Contracts\\Cache\\Repository',
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
        'endColumn' => 48,
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
          'cacheRepository' => 
          array (
            'name' => 'cacheRepository',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Contracts\\Cache\\Repository',
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
            'endColumn' => 48,
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
        'namespace' => 'App\\Infrastructure\\Authorization\\EventHandler',
        'declaringClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
        'implementingClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
        'currentClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'domainEvent' => 
          array (
            'name' => 'domainEvent',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Contract\\Event\\DomainEvent',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 19,
            'endLine' => 19,
            'startColumn' => 28,
            'endColumn' => 51,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 19,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Infrastructure\\Authorization\\EventHandler',
        'declaringClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
        'implementingClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
        'currentClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnRoleAssigned',
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