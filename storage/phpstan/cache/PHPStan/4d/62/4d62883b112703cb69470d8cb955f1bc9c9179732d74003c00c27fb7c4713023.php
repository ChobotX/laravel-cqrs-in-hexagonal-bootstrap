<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/Query/GetAvailableModules/GetAvailableModulesHandler.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesHandler
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-1fe7ea1cc01f4898c4451180633ca10967814e96c4edc957e17114b50852f770',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
        'filename' => '/var/www/html/app/Domain/Authorization/Query/GetAvailableModules/GetAvailableModulesHandler.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules',
    'name' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
    'shortName' => 'GetAvailableModulesHandler',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => '/** @implements QueryHandler<GetAvailableModulesQuery, array<string, array{label: string, features: array<string, array{label: string, actions: list<string>}>}>> */',
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 25,
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
      'modules' => 
      array (
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
        'name' => 'modules',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 9,
        'endColumn' => 30,
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
          'modules' => 
          array (
            'name' => 'modules',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 17,
            'endLine' => 17,
            'startColumn' => 9,
            'endColumn' => 30,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array<string, array{label: string, features: array<string, array{label: string, actions: list<string>}>}>  $modules
 */',
        'startLine' => 16,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
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
            'startLine' => 21,
            'endLine' => 21,
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
        'docComment' => '/** @return array<string, array{label: string, features: array<string, array{label: string, actions: list<string>}>}> */',
        'startLine' => 21,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules',
        'declaringClassName' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
        'implementingClassName' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
        'currentClassName' => 'App\\Domain\\Authorization\\Query\\GetAvailableModules\\GetAvailableModulesHandler',
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