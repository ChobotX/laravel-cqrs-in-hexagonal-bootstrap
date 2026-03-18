<?php declare(strict_types = 1);

// osfsl-/var/www/html/vendor/composer/../mockery/mockery/library/Mockery/Expectation.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Mockery\Expectation
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-33940ef870e662d3d63ac4d3559f2c4e99f9db5f8f7fb2af5d2fe7c9f02faa0e-8.5.3-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Mockery\\Expectation',
        'filename' => '/var/www/html/vendor/composer/../mockery/mockery/library/Mockery/Expectation.php',
      ),
    ),
    'namespace' => 'Mockery',
    'name' => 'Mockery\\Expectation',
    'shortName' => 'Expectation',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 49,
    'endLine' => 1085,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Mockery\\ExpectationInterface',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'ERROR_ZERO_INVOCATION' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => 'ERROR_ZERO_INVOCATION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'shouldNotReceive(), never(), times(0) chaining additional invocation count methods has been deprecated and will throw an exception in a future version of Mockery\'',
          'attributes' => 
          array (
            'startLine' => 51,
            'endLine' => 51,
            'startTokenPos' => 224,
            'startFilePos' => 1315,
            'endTokenPos' => 224,
            'endFilePos' => 1477,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 205,
      ),
    ),
    'immediateProperties' => 
    array (
      '_actualCount' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_actualCount',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 235,
            'startFilePos' => 1599,
            'endTokenPos' => 235,
            'endFilePos' => 1599,
          ),
        ),
        'docComment' => '/**
 * Actual count of calls to this expectation
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_because' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_because',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 65,
            'startTokenPos' => 246,
            'startFilePos' => 1701,
            'endTokenPos' => 246,
            'endFilePos' => 1704,
          ),
        ),
        'docComment' => '/**
 * Exception message
 *
 * @var null|string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_closureQueue' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_closureQueue',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 73,
            'endLine' => 73,
            'startTokenPos' => 257,
            'startFilePos' => 1878,
            'endTokenPos' => 258,
            'endFilePos' => 1879,
          ),
        ),
        'docComment' => '/**
 * Array of closures executed with given arguments to generate a result
 * to be returned
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_countValidatorClass' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_countValidatorClass',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\\Mockery\\CountValidator\\Exact::class',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 269,
            'startFilePos' => 2003,
            'endTokenPos' => 271,
            'endFilePos' => 2014,
          ),
        ),
        'docComment' => '/**
 * The count validator class to use
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 51,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_countValidators' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_countValidators',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 282,
            'startFilePos' => 2122,
            'endTokenPos' => 283,
            'endFilePos' => 2123,
          ),
        ),
        'docComment' => '/**
 * Count validator store
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 37,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_expectedArgs' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_expectedArgs',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 94,
            'endLine' => 94,
            'startTokenPos' => 294,
            'startFilePos' => 2245,
            'endTokenPos' => 295,
            'endFilePos' => 2246,
          ),
        ),
        'docComment' => '/**
 * Arguments expected by this expectation
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 94,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_expectedCount' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_expectedCount',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '-1',
          'attributes' => 
          array (
            'startLine' => 101,
            'endLine' => 101,
            'startTokenPos' => 306,
            'startFilePos' => 2372,
            'endTokenPos' => 307,
            'endFilePos' => 2373,
          ),
        ),
        'docComment' => '/**
 * Expected count of calls to this expectation
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 101,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_globally' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_globally',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 109,
            'endLine' => 109,
            'startTokenPos' => 318,
            'startFilePos' => 2537,
            'endTokenPos' => 318,
            'endFilePos' => 2541,
          ),
        ),
        'docComment' => '/**
 * Flag indicating whether the order of calling is determined locally or
 * globally
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 109,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_globalOrderNumber' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_globalOrderNumber',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 116,
            'endLine' => 116,
            'startTokenPos' => 329,
            'startFilePos' => 2701,
            'endTokenPos' => 329,
            'endFilePos' => 2704,
          ),
        ),
        'docComment' => '/**
 * Integer representing the call order of this expectation on a global basis
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 116,
        'endLine' => 116,
        'startColumn' => 5,
        'endColumn' => 41,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_mock' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_mock',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 123,
            'endLine' => 123,
            'startTokenPos' => 340,
            'startFilePos' => 2839,
            'endTokenPos' => 340,
            'endFilePos' => 2842,
          ),
        ),
        'docComment' => '/**
 * Mock object to which this expectation belongs
 *
 * @var LegacyMockInterface
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 123,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_name' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_name',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 130,
            'endLine' => 130,
            'startTokenPos' => 351,
            'startFilePos' => 2930,
            'endTokenPos' => 351,
            'endFilePos' => 2933,
          ),
        ),
        'docComment' => '/**
 * Method name
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 130,
        'endLine' => 130,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_orderNumber' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_orderNumber',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 137,
            'endLine' => 137,
            'startTokenPos' => 362,
            'startFilePos' => 3069,
            'endTokenPos' => 362,
            'endFilePos' => 3072,
          ),
        ),
        'docComment' => '/**
 * Integer representing the call order of this expectation
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 137,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_passthru' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_passthru',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 145,
            'endLine' => 145,
            'startTokenPos' => 373,
            'startFilePos' => 3304,
            'endTokenPos' => 373,
            'endFilePos' => 3308,
          ),
        ),
        'docComment' => '/**
 * Flag indicating if the return value should be obtained from the original
 * class method instead of returning predefined values from the return queue
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 145,
        'endLine' => 145,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_returnQueue' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_returnQueue',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 152,
            'endLine' => 152,
            'startTokenPos' => 384,
            'startFilePos' => 3453,
            'endTokenPos' => 385,
            'endFilePos' => 3454,
          ),
        ),
        'docComment' => '/**
 * Array of return values as a queue for multiple return sequence
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 152,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_returnValue' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_returnValue',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 159,
            'endLine' => 159,
            'startTokenPos' => 396,
            'startFilePos' => 3574,
            'endTokenPos' => 396,
            'endFilePos' => 3577,
          ),
        ),
        'docComment' => '/**
 * Value to return from this expectation
 *
 * @var mixed
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 159,
        'endLine' => 159,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_setQueue' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_setQueue',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 166,
            'endLine' => 166,
            'startTokenPos' => 407,
            'startFilePos' => 3712,
            'endTokenPos' => 408,
            'endFilePos' => 3713,
          ),
        ),
        'docComment' => '/**
 * Array of values to be set when this expectation matches
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 166,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      '_throw' => 
      array (
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'name' => '_throw',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 173,
            'endLine' => 173,
            'startTokenPos' => 419,
            'startFilePos' => 3861,
            'endTokenPos' => 419,
            'endFilePos' => 3865,
          ),
        ),
        'docComment' => '/**
 * Flag indicating that an exception is expected to be throw (not returned)
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 173,
        'endLine' => 173,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
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
          'mock' => 
          array (
            'name' => 'mock',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Mockery\\LegacyMockInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 180,
            'endLine' => 180,
            'startColumn' => 33,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 180,
            'endLine' => 180,
            'startColumn' => 60,
            'endColumn' => 64,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Constructor
 *
 * @param string $name
 */',
        'startLine' => 180,
        'endLine' => 185,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      '__clone' => 
      array (
        'name' => '__clone',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Cloning logic
 */',
        'startLine' => 190,
        'endLine' => 201,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      '__toString' => 
      array (
        'name' => '__toString',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a string with the method name and arguments formatted
 *
 * @return string
 */',
        'startLine' => 208,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturn' => 
      array (
        'name' => 'andReturn',
        'parameters' => 
        array (
          'args' => 
          array (
            'name' => 'args',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 31,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set a return value, or sequential queue of return values
 *
 * @param mixed ...$args
 *
 * @return self
 */',
        'startLine' => 220,
        'endLine' => 225,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturnArg' => 
      array (
        'name' => 'andReturnArg',
        'parameters' => 
        array (
          'index' => 
          array (
            'name' => 'index',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 34,
            'endColumn' => 39,
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
 * Sets up a closure to return the nth argument from the expected method call
 *
 * @param int $index
 *
 * @return self
 */',
        'startLine' => 234,
        'endLine' => 255,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturnFalse' => 
      array (
        'name' => 'andReturnFalse',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return self
 */',
        'startLine' => 260,
        'endLine' => 263,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturnNull' => 
      array (
        'name' => 'andReturnNull',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return null. This is merely a language construct for Mock describing.
 *
 * @return self
 */',
        'startLine' => 270,
        'endLine' => 273,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturns' => 
      array (
        'name' => 'andReturns',
        'parameters' => 
        array (
          'args' => 
          array (
            'name' => 'args',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 282,
            'endLine' => 282,
            'startColumn' => 32,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set a return value, or sequential queue of return values
 *
 * @param mixed ...$args
 *
 * @return self
 */',
        'startLine' => 282,
        'endLine' => 285,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturnSelf' => 
      array (
        'name' => 'andReturnSelf',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return this mock, like a fluent interface
 *
 * @return self
 */',
        'startLine' => 292,
        'endLine' => 295,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturnTrue' => 
      array (
        'name' => 'andReturnTrue',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return self
 */',
        'startLine' => 300,
        'endLine' => 303,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturnUndefined' => 
      array (
        'name' => 'andReturnUndefined',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return a self-returning black hole object.
 *
 * @return self
 */',
        'startLine' => 310,
        'endLine' => 313,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturnUsing' => 
      array (
        'name' => 'andReturnUsing',
        'parameters' => 
        array (
          'args' => 
          array (
            'name' => 'args',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 324,
            'endLine' => 324,
            'startColumn' => 36,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set a closure or sequence of closures with which to generate return
 * values. The arguments passed to the expected method are passed to the
 * closures as parameters.
 *
 * @param callable ...$args
 *
 * @return self
 */',
        'startLine' => 324,
        'endLine' => 329,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andReturnValues' => 
      array (
        'name' => 'andReturnValues',
        'parameters' => 
        array (
          'values' => 
          array (
            'name' => 'values',
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
            'startLine' => 336,
            'endLine' => 336,
            'startColumn' => 37,
            'endColumn' => 49,
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
 * Set a sequential queue of return values with an array
 *
 * @return self
 */',
        'startLine' => 336,
        'endLine' => 339,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andSet' => 
      array (
        'name' => 'andSet',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 349,
            'endLine' => 349,
            'startColumn' => 28,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 349,
            'endLine' => 349,
            'startColumn' => 35,
            'endColumn' => 44,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Register values to be set to a public property each time this expectation occurs
 *
 * @param string $name
 * @param array  ...$values
 *
 * @return self
 */',
        'startLine' => 349,
        'endLine' => 354,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andThrow' => 
      array (
        'name' => 'andThrow',
        'parameters' => 
        array (
          'exception' => 
          array (
            'name' => 'exception',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 365,
            'endLine' => 365,
            'startColumn' => 30,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 365,
                'endLine' => 365,
                'startTokenPos' => 985,
                'startFilePos' => 8025,
                'endTokenPos' => 985,
                'endFilePos' => 8026,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 365,
            'endLine' => 365,
            'startColumn' => 42,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'code' => 
          array (
            'name' => 'code',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 365,
                'endLine' => 365,
                'startTokenPos' => 992,
                'startFilePos' => 8037,
                'endTokenPos' => 992,
                'endFilePos' => 8037,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 365,
            'endLine' => 365,
            'startColumn' => 57,
            'endColumn' => 65,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'previous' => 
          array (
            'name' => 'previous',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 365,
                'endLine' => 365,
                'startTokenPos' => 1002,
                'startFilePos' => 8064,
                'endTokenPos' => 1002,
                'endFilePos' => 8067,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'Exception',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 365,
            'endLine' => 365,
            'startColumn' => 68,
            'endColumn' => 95,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set Exception class and arguments to that class to be thrown
 *
 * @param string|Throwable $exception
 * @param string           $message
 * @param int              $code
 *
 * @return self
 */',
        'startLine' => 365,
        'endLine' => 374,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andThrowExceptions' => 
      array (
        'name' => 'andThrowExceptions',
        'parameters' => 
        array (
          'exceptions' => 
          array (
            'name' => 'exceptions',
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
            'startLine' => 381,
            'endLine' => 381,
            'startColumn' => 40,
            'endColumn' => 56,
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
 * Set Exception classes to be thrown
 *
 * @return self
 */',
        'startLine' => 381,
        'endLine' => 392,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andThrows' => 
      array (
        'name' => 'andThrows',
        'parameters' => 
        array (
          'exception' => 
          array (
            'name' => 'exception',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 394,
            'endLine' => 394,
            'startColumn' => 31,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 394,
                'endLine' => 394,
                'startTokenPos' => 1150,
                'startFilePos' => 8800,
                'endTokenPos' => 1150,
                'endFilePos' => 8801,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 394,
            'endLine' => 394,
            'startColumn' => 43,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'code' => 
          array (
            'name' => 'code',
            'default' => 
            array (
              'code' => '0',
              'attributes' => 
              array (
                'startLine' => 394,
                'endLine' => 394,
                'startTokenPos' => 1157,
                'startFilePos' => 8812,
                'endTokenPos' => 1157,
                'endFilePos' => 8812,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 394,
            'endLine' => 394,
            'startColumn' => 58,
            'endColumn' => 66,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'previous' => 
          array (
            'name' => 'previous',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 394,
                'endLine' => 394,
                'startTokenPos' => 1167,
                'startFilePos' => 8839,
                'endTokenPos' => 1167,
                'endFilePos' => 8842,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'Exception',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 394,
            'endLine' => 394,
            'startColumn' => 69,
            'endColumn' => 96,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 394,
        'endLine' => 397,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'andYield' => 
      array (
        'name' => 'andYield',
        'parameters' => 
        array (
          'args' => 
          array (
            'name' => 'args',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 406,
            'endLine' => 406,
            'startColumn' => 30,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sets up a closure that will yield each of the provided args
 *
 * @param mixed ...$args
 *
 * @return self
 */',
        'startLine' => 406,
        'endLine' => 417,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => true,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'atLeast' => 
      array (
        'name' => 'atLeast',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sets next count validator to the AtLeast instance
 *
 * @return self
 */',
        'startLine' => 424,
        'endLine' => 429,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'atMost' => 
      array (
        'name' => 'atMost',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sets next count validator to the AtMost instance
 *
 * @return self
 */',
        'startLine' => 436,
        'endLine' => 441,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'because' => 
      array (
        'name' => 'because',
        'parameters' => 
        array (
          'message' => 
          array (
            'name' => 'message',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 450,
            'endLine' => 450,
            'startColumn' => 29,
            'endColumn' => 36,
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
 * Set the exception message
 *
 * @param string $message
 *
 * @return $this
 */',
        'startLine' => 450,
        'endLine' => 455,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'between' => 
      array (
        'name' => 'between',
        'parameters' => 
        array (
          'minimum' => 
          array (
            'name' => 'minimum',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 463,
            'endLine' => 463,
            'startColumn' => 29,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'maximum' => 
          array (
            'name' => 'maximum',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 463,
            'endLine' => 463,
            'startColumn' => 39,
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
        'docComment' => '/**
 * Shorthand for setting minimum and maximum constraints on call counts
 *
 * @param int $minimum
 * @param int $maximum
 */',
        'startLine' => 463,
        'endLine' => 466,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'byDefault' => 
      array (
        'name' => 'byDefault',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mark this expectation as being a default
 *
 * @return self
 */',
        'startLine' => 473,
        'endLine' => 482,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'getExceptionMessage' => 
      array (
        'name' => 'getExceptionMessage',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return null|string
 */',
        'startLine' => 487,
        'endLine' => 490,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'getMock' => 
      array (
        'name' => 'getMock',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return the parent mock of the expectation
 *
 * @return LegacyMockInterface|MockInterface
 */',
        'startLine' => 497,
        'endLine' => 500,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'getName' => 
      array (
        'name' => 'getName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 502,
        'endLine' => 505,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'getOrderNumber' => 
      array (
        'name' => 'getOrderNumber',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return order number
 *
 * @return int
 */',
        'startLine' => 512,
        'endLine' => 515,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'globally' => 
      array (
        'name' => 'globally',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicates call order should apply globally
 *
 * @return self
 */',
        'startLine' => 522,
        'endLine' => 527,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'isCallCountConstrained' => 
      array (
        'name' => 'isCallCountConstrained',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if there is a constraint on call count
 *
 * @return bool
 */',
        'startLine' => 534,
        'endLine' => 537,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'isEligible' => 
      array (
        'name' => 'isEligible',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Checks if this expectation is eligible for additional calls
 *
 * @return bool
 */',
        'startLine' => 544,
        'endLine' => 553,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'matchArgs' => 
      array (
        'name' => 'matchArgs',
        'parameters' => 
        array (
          'args' => 
          array (
            'name' => 'args',
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
            'startLine' => 560,
            'endLine' => 560,
            'startColumn' => 31,
            'endColumn' => 41,
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
 * Check if passed arguments match an argument expectation
 *
 * @return bool
 */',
        'startLine' => 560,
        'endLine' => 585,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'never' => 
      array (
        'name' => 'never',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicates that this expectation is never expected to be called
 *
 * @return self
 */',
        'startLine' => 592,
        'endLine' => 595,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'once' => 
      array (
        'name' => 'once',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicates that this expectation is expected exactly once
 *
 * @return self
 */',
        'startLine' => 602,
        'endLine' => 605,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'ordered' => 
      array (
        'name' => 'ordered',
        'parameters' => 
        array (
          'group' => 
          array (
            'name' => 'group',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 614,
                'endLine' => 614,
                'startTokenPos' => 1882,
                'startFilePos' => 13408,
                'endTokenPos' => 1882,
                'endFilePos' => 13411,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 614,
            'endLine' => 614,
            'startColumn' => 29,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicates that this expectation must be called in a specific given order
 *
 * @param string $group Name of the ordered group
 *
 * @return self
 */',
        'startLine' => 614,
        'endLine' => 625,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'passthru' => 
      array (
        'name' => 'passthru',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Flag this expectation as calling the original class method with
 * the provided arguments instead of using a return value queue.
 *
 * @return self
 */',
        'startLine' => 633,
        'endLine' => 644,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'set' => 
      array (
        'name' => 'set',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 655,
            'endLine' => 655,
            'startColumn' => 25,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 655,
            'endLine' => 655,
            'startColumn' => 32,
            'endColumn' => 37,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Alias to andSet(). Allows the natural English construct
 * - set(\'foo\', \'bar\')->andReturn(\'bar\')
 *
 * @param string $name
 * @param mixed  $value
 *
 * @return self
 */',
        'startLine' => 655,
        'endLine' => 658,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'times' => 
      array (
        'name' => 'times',
        'parameters' => 
        array (
          'limit' => 
          array (
            'name' => 'limit',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 669,
                'endLine' => 669,
                'startTokenPos' => 2062,
                'startFilePos' => 14793,
                'endTokenPos' => 2062,
                'endFilePos' => 14796,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 669,
            'endLine' => 669,
            'startColumn' => 27,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicates the number of times this expectation should occur
 *
 * @param int $limit
 *
 * @throws InvalidArgumentException
 *
 * @return self
 */',
        'startLine' => 669,
        'endLine' => 699,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'twice' => 
      array (
        'name' => 'twice',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicates that this expectation is expected exactly twice
 *
 * @return self
 */',
        'startLine' => 706,
        'endLine' => 709,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'validateOrder' => 
      array (
        'name' => 'validateOrder',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Verify call order
 *
 * @return void
 */',
        'startLine' => 716,
        'endLine' => 729,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'verify' => 
      array (
        'name' => 'verify',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Verify this expectation
 *
 * @return void
 */',
        'startLine' => 736,
        'endLine' => 741,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'verifyCall' => 
      array (
        'name' => 'verifyCall',
        'parameters' => 
        array (
          'args' => 
          array (
            'name' => 'args',
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
            'startLine' => 751,
            'endLine' => 751,
            'startColumn' => 32,
            'endColumn' => 42,
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
 * Verify the current call, i.e. that the given arguments match those
 * of this expectation
 *
 * @throws Throwable
 *
 * @return mixed
 */',
        'startLine' => 751,
        'endLine' => 768,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'with' => 
      array (
        'name' => 'with',
        'parameters' => 
        array (
          'args' => 
          array (
            'name' => 'args',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 777,
            'endLine' => 777,
            'startColumn' => 26,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Expected argument setter for the expectation
 *
 * @param mixed ...$args
 *
 * @return self
 */',
        'startLine' => 777,
        'endLine' => 780,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'withAnyArgs' => 
      array (
        'name' => 'withAnyArgs',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set expectation that any arguments are acceptable
 *
 * @return self
 */',
        'startLine' => 787,
        'endLine' => 792,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'withArgs' => 
      array (
        'name' => 'withArgs',
        'parameters' => 
        array (
          'argsOrClosure' => 
          array (
            'name' => 'argsOrClosure',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 802,
            'endLine' => 802,
            'startColumn' => 30,
            'endColumn' => 43,
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
 * Expected arguments for the expectation passed as an array or a closure that matches each passed argument on
 * each function call.
 *
 * @param array|Closure $argsOrClosure
 *
 * @return self
 */',
        'startLine' => 802,
        'endLine' => 817,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'withNoArgs' => 
      array (
        'name' => 'withNoArgs',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set with() as no arguments expected
 *
 * @return self
 */',
        'startLine' => 824,
        'endLine' => 829,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'withSomeOfArgs' => 
      array (
        'name' => 'withSomeOfArgs',
        'parameters' => 
        array (
          'expectedArgs' => 
          array (
            'name' => 'expectedArgs',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 838,
            'endLine' => 838,
            'startColumn' => 36,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Expected arguments should partially match the real arguments
 *
 * @param mixed ...$expectedArgs
 *
 * @return self
 */',
        'startLine' => 838,
        'endLine' => 849,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'zeroOrMoreTimes' => 
      array (
        'name' => 'zeroOrMoreTimes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicates this expectation should occur zero or more times
 *
 * @return self
 */',
        'startLine' => 856,
        'endLine' => 859,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      '_defineOrdered' => 
      array (
        'name' => '_defineOrdered',
        'parameters' => 
        array (
          'group' => 
          array (
            'name' => 'group',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 869,
            'endLine' => 869,
            'startColumn' => 39,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'ordering' => 
          array (
            'name' => 'ordering',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 869,
            'endLine' => 869,
            'startColumn' => 47,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Setup the ordering tracking on the mock or mock container
 *
 * @param string $group
 * @param object $ordering
 *
 * @return int
 */',
        'startLine' => 869,
        'endLine' => 885,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      '_getReturnValue' => 
      array (
        'name' => '_getReturnValue',
        'parameters' => 
        array (
          'args' => 
          array (
            'name' => 'args',
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
            'startLine' => 892,
            'endLine' => 892,
            'startColumn' => 40,
            'endColumn' => 50,
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
 * Fetch the return value for the matching args
 *
 * @return mixed
 */',
        'startLine' => 892,
        'endLine' => 915,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      '_matchArg' => 
      array (
        'name' => '_matchArg',
        'parameters' => 
        array (
          'expected' => 
          array (
            'name' => 'expected',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 925,
            'endLine' => 925,
            'startColumn' => 34,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'actual' => 
          array (
            'name' => 'actual',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 925,
            'endLine' => 925,
            'startColumn' => 45,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if passed argument matches an argument expectation
 *
 * @param mixed $expected
 * @param mixed $actual
 *
 * @return bool
 */',
        'startLine' => 925,
        'endLine' => 956,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      '_matchArgs' => 
      array (
        'name' => '_matchArgs',
        'parameters' => 
        array (
          'args' => 
          array (
            'name' => 'args',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 965,
            'endLine' => 965,
            'startColumn' => 35,
            'endColumn' => 39,
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
 * Check if the passed arguments match the expectations, one by one.
 *
 * @param array $args
 *
 * @return bool
 */',
        'startLine' => 965,
        'endLine' => 976,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      '_setValues' => 
      array (
        'name' => '_setValues',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sets public properties with queued values to the mock object
 *
 * @return void
 */',
        'startLine' => 983,
        'endLine' => 1012,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'isAndAnyOtherArgumentsMatcher' => 
      array (
        'name' => 'isAndAnyOtherArgumentsMatcher',
        'parameters' => 
        array (
          'expectedArg' => 
          array (
            'name' => 'expectedArg',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1021,
            'endLine' => 1021,
            'startColumn' => 52,
            'endColumn' => 63,
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
 * @template TExpectedArg
 *
 * @param TExpectedArg $expectedArg
 *
 * @return bool
 */',
        'startLine' => 1021,
        'endLine' => 1024,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'isArgumentListMatcher' => 
      array (
        'name' => 'isArgumentListMatcher',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check if the registered expectation is an ArgumentListMatcher
 *
 * @return bool
 */',
        'startLine' => 1031,
        'endLine' => 1034,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'throwAsNecessary' => 
      array (
        'name' => 'throwAsNecessary',
        'parameters' => 
        array (
          'return' => 
          array (
            'name' => 'return',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1045,
            'endLine' => 1045,
            'startColumn' => 39,
            'endColumn' => 45,
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
 * Throws an exception if the expectation has been configured to do so
 *
 * @param Throwable $return
 *
 * @throws Throwable
 *
 * @return void
 */',
        'startLine' => 1045,
        'endLine' => 1056,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'withArgsInArray' => 
      array (
        'name' => 'withArgsInArray',
        'parameters' => 
        array (
          'arguments' => 
          array (
            'name' => 'arguments',
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
            'startLine' => 1063,
            'endLine' => 1063,
            'startColumn' => 38,
            'endColumn' => 53,
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
 * Expected arguments for the expectation passed as an array
 *
 * @return self
 */',
        'startLine' => 1063,
        'endLine' => 1072,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
        'aliasName' => NULL,
      ),
      'withArgsMatchedByClosure' => 
      array (
        'name' => 'withArgsMatchedByClosure',
        'parameters' => 
        array (
          'closure' => 
          array (
            'name' => 'closure',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1079,
            'endLine' => 1079,
            'startColumn' => 47,
            'endColumn' => 62,
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
 * Expected arguments have to be matched by the given closure.
 *
 * @return self
 */',
        'startLine' => 1079,
        'endLine' => 1084,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Mockery',
        'declaringClassName' => 'Mockery\\Expectation',
        'implementingClassName' => 'Mockery\\Expectation',
        'currentClassName' => 'Mockery\\Expectation',
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