<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Command/ShareRecord/ShareRecordHandler.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Command\ShareRecord\ShareRecordHandler
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-1d254fde3748b396524388f3df508105bc3b50176723cffd7282ee3bcc995929',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'filename' => '/var/www/html/app/Domain/Authorization/Command/ShareRecord/ShareRecordHandler.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Command\\ShareRecord',
    'name' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
    'shortName' => 'ShareRecordHandler',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements CommandHandler<ShareRecordCommand> */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 47,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'App\\Contract\\Command\\CommandHandler',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'recordShareRepository' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'name' => 'recordShareRepository',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Domain\\Authorization\\RecordShareRepository',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 9,
        'endColumn' => 60,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'eventCollector' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'name' => 'eventCollector',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Contract\\Event\\EventCollector',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 9,
        'endColumn' => 46,
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
          'recordShareRepository' => 
          array (
            'name' => 'recordShareRepository',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Domain\\Authorization\\RecordShareRepository',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 20,
            'endLine' => 20,
            'startColumn' => 9,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'eventCollector' => 
          array (
            'name' => 'eventCollector',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Contract\\Event\\EventCollector',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 9,
            'endColumn' => 46,
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
        'startLine' => 19,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Command\\ShareRecord',
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Contract\\Command\\Command',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 28,
            'endColumn' => 43,
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
        'startLine' => 24,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Command\\ShareRecord',
        'declaringClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Command\\ShareRecord\\ShareRecordHandler',
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