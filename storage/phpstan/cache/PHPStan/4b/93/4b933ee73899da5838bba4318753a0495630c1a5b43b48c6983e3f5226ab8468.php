<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Infrastructure/Authorization/EventHandler/InvalidateCacheOnOverrideSet.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnOverrideSet
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-607d5ea18f8e3323df3f49df2943d0bbf352dae22c024bdfe5e2072b818ebf04',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
        'filename' => '/var/www/html/app/Infrastructure/Authorization/EventHandler/InvalidateCacheOnOverrideSet.php',
      ),
    ),
    'namespace' => 'App\\Infrastructure\\Authorization\\EventHandler',
    'name' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
    'shortName' => 'InvalidateCacheOnOverrideSet',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements DomainEventHandler<PermissionOverrideSet> */',
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
        'declaringClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
        'implementingClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
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
        'declaringClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
        'implementingClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
        'currentClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
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
        'declaringClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
        'implementingClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
        'currentClassName' => 'App\\Infrastructure\\Authorization\\EventHandler\\InvalidateCacheOnOverrideSet',
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