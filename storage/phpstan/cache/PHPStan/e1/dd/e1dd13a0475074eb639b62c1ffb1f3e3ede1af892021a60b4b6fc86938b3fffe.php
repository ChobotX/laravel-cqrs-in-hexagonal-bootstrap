<?php declare(strict_types = 1);

// odsl-/var/www/html/tests/Architecture/PHPStan/NoLaravelHelpersInDomainRule.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Tests\Architecture\PHPStan\NoLaravelHelpersInDomainRule
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.65.0.9-8.5.3-f7d82c6dec07353c0e1ab252bc27b58512566882589ee1f09eedd53af4e8ee8e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
        'filename' => '/var/www/html/tests/Architecture/PHPStan/NoLaravelHelpersInDomainRule.php',
      ),
    ),
    'namespace' => 'Tests\\Architecture\\PHPStan',
    'name' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
    'shortName' => 'NoLaravelHelpersInDomainRule',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * @implements Rule<FuncCall>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 20,
    'endLine' => 92,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'PHPStan\\Rules\\Rule',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'BLOCKED_HELPERS' => 
      array (
        'declaringClassName' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
        'implementingClassName' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
        'name' => 'BLOCKED_HELPERS',
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
        'value' => 
        array (
          'code' => '[\'__\', \'trans\', \'trans_choice\', \'app\', \'resolve\', \'config\', \'session\', \'cache\', \'event\', \'dispatch\', \'redirect\', \'response\', \'view\', \'route\', \'url\', \'abort\', \'abort_if\', \'abort_unless\', \'logger\', \'info\', \'report\', \'request\', \'cookie\', \'old\', \'now\', \'today\', \'auth\', \'back\', \'bcrypt\', \'encrypt\', \'decrypt\', \'validator\', \'rescue\', \'retry\', \'throw_if\', \'throw_unless\']',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 59,
            'startTokenPos' => 83,
            'startFilePos' => 433,
            'endTokenPos' => 193,
            'endFilePos' => 1092,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'getNodeType' => 
      array (
        'name' => 'getNodeType',
        'parameters' => 
        array (
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
        'startLine' => 61,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Tests\\Architecture\\PHPStan',
        'declaringClassName' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
        'implementingClassName' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
        'currentClassName' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
        'aliasName' => NULL,
      ),
      'processNode' => 
      array (
        'name' => 'processNode',
        'parameters' => 
        array (
          'node' => 
          array (
            'name' => 'node',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'PhpParser\\Node',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 66,
            'endLine' => 66,
            'startColumn' => 33,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'scope' => 
          array (
            'name' => 'scope',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'PHPStan\\Analyser\\Scope',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 66,
            'endLine' => 66,
            'startColumn' => 45,
            'endColumn' => 56,
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
        'docComment' => NULL,
        'startLine' => 66,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Tests\\Architecture\\PHPStan',
        'declaringClassName' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
        'implementingClassName' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
        'currentClassName' => 'Tests\\Architecture\\PHPStan\\NoLaravelHelpersInDomainRule',
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