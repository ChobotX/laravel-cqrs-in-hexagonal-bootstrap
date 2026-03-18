<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Contract/Event/DomainEventHandler.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Contract\Event\DomainEventHandler
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-ffdbb371bc01a68acbe73f0f4bef6b02a1edf64086686783e94868caa367cb02',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Contract\\Event\\DomainEventHandler',
        'filename' => '/var/www/html/app/Contract/Event/DomainEventHandler.php',
      ),
    ),
    'namespace' => 'App\\Contract\\Event',
    'name' => 'App\\Contract\\Event\\DomainEventHandler',
    'shortName' => 'DomainEventHandler',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @template TEvent of DomainEvent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 16,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
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
            'startLine' => 15,
            'endLine' => 15,
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
        'docComment' => '/**
 * @param  TEvent  $domainEvent
 */',
        'startLine' => 15,
        'endLine' => 15,
        'startColumn' => 5,
        'endColumn' => 59,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Contract\\Event',
        'declaringClassName' => 'App\\Contract\\Event\\DomainEventHandler',
        'implementingClassName' => 'App\\Contract\\Event\\DomainEventHandler',
        'currentClassName' => 'App\\Contract\\Event\\DomainEventHandler',
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