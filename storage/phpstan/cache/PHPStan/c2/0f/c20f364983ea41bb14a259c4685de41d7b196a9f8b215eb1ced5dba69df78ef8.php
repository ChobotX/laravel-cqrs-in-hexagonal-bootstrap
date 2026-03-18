<?php declare(strict_types = 1);

// odsl-/var/www/html/app/Domain/Authorization/ModuleConfigExpander.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Domain\Authorization\ModuleConfigExpander
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-eece3abfe43aec733083304f908cfa7640a84b9e2e635054d2e6b3a76f45a462',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'filename' => '/var/www/html/app/Domain/Authorization/ModuleConfigExpander.php',
      ),
    ),
    'namespace' => 'App\\Domain\\Authorization',
    'name' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
    'shortName' => 'ModuleConfigExpander',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 65568,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 7,
    'endLine' => 90,
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
      'expandToLeaves' => 
      array (
        'name' => 'expandToLeaves',
        'parameters' => 
        array (
          'permissionKey' => 
          array (
            'name' => 'permissionKey',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Domain\\Authorization\\PermissionKey',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 13,
            'endLine' => 13,
            'startColumn' => 36,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'availableModules' => 
          array (
            'name' => 'availableModules',
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
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 13,
            'endLine' => 13,
            'startColumn' => 66,
            'endColumn' => 88,
            'parameterIndex' => 1,
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
        'docComment' => '/**
 * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
 * @return list<string>
 */',
        'startLine' => 13,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization',
        'declaringClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'implementingClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'currentClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'aliasName' => NULL,
      ),
      'allLeafKeys' => 
      array (
        'name' => 'allLeafKeys',
        'parameters' => 
        array (
          'availableModules' => 
          array (
            'name' => 'availableModules',
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
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 44,
            'endLine' => 44,
            'startColumn' => 33,
            'endColumn' => 55,
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
        'docComment' => '/**
 * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
 * @return list<array{string, string, string}>
 */',
        'startLine' => 44,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Domain\\Authorization',
        'declaringClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'implementingClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'currentClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'aliasName' => NULL,
      ),
      'allModuleLeafKeys' => 
      array (
        'name' => 'allModuleLeafKeys',
        'parameters' => 
        array (
          'moduleName' => 
          array (
            'name' => 'moduleName',
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
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 63,
            'endLine' => 63,
            'startColumn' => 40,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'moduleConfig' => 
          array (
            'name' => 'moduleConfig',
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
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 63,
            'endLine' => 63,
            'startColumn' => 60,
            'endColumn' => 78,
            'parameterIndex' => 1,
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
        'docComment' => '/**
 * @param  array{features: array<string, array{actions: list<string>}>}  $moduleConfig
 * @return list<string>
 */',
        'startLine' => 63,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Domain\\Authorization',
        'declaringClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'implementingClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'currentClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'aliasName' => NULL,
      ),
      'allFeatureLeafKeys' => 
      array (
        'name' => 'allFeatureLeafKeys',
        'parameters' => 
        array (
          'moduleName' => 
          array (
            'name' => 'moduleName',
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
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 80,
            'endLine' => 80,
            'startColumn' => 41,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'featureName' => 
          array (
            'name' => 'featureName',
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
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 80,
            'endLine' => 80,
            'startColumn' => 61,
            'endColumn' => 79,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'featureConfig' => 
          array (
            'name' => 'featureConfig',
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
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 80,
            'endLine' => 80,
            'startColumn' => 82,
            'endColumn' => 101,
            'parameterIndex' => 2,
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
        'docComment' => '/**
 * @param  array{actions: list<string>}  $featureConfig
 * @return list<string>
 */',
        'startLine' => 80,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Domain\\Authorization',
        'declaringClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'implementingClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
        'currentClassName' => 'App\\Domain\\Authorization\\ModuleConfigExpander',
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