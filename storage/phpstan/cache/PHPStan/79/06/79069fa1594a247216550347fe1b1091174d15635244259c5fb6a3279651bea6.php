<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Query/GetRecordShares/GetRecordSharesHandler.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Query\GetRecordShares\GetRecordSharesHandler
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-bcdc0402bc086591ce5f5af6ff7e9e146cdf1262687d6fcc3e33bc5f70804446',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
        'filename' => '/var/www/html/app/Domain/Authorization/Query/GetRecordShares/GetRecordSharesHandler.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Query\\GetRecordShares',
    'name' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
    'shortName' => 'GetRecordSharesHandler',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements QueryHandler<GetRecordSharesQuery, list<RecordShare>> */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 28,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'App\\Contract\\Query\\QueryHandler',
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
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
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
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 9,
        'endColumn' => 60,
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
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 9,
            'endColumn' => 60,
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
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetRecordShares',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Contract\\Query\\Query',
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
            'startColumn' => 28,
            'endColumn' => 39,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @return list<RecordShare> */',
        'startLine' => 20,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetRecordShares',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetRecordShares\\GetRecordSharesHandler',
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