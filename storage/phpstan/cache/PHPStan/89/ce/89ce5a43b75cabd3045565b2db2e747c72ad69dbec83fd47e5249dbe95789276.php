<?php declare(strict_types = 1);

// ftm-/var/www/html/app/Domain/Authorization/Command/AssignRoleToUser/AssignRoleToUserHandler.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v4-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      '6aef807ad28646970141fddbc2c5d7ce' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser',
         'uses' => 
        array (
          'command' => 'App\\Contract\\Command\\Command',
          'commandhandler' => 'App\\Contract\\Command\\CommandHandler',
          'eventcollector' => 'App\\Contract\\Event\\EventCollector',
          'roleassignedtouser' => 'App\\Domain\\Authorization\\Event\\RoleAssignedToUser',
          'duplicateroleassignmentexception' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
          'rolenotfoundexception' => 'App\\Domain\\Authorization\\Exception\\RoleNotFoundException',
          'role' => 'App\\Domain\\Authorization\\Role',
          'roleid' => 'App\\Domain\\Authorization\\RoleId',
          'rolerepository' => 'App\\Domain\\Authorization\\RoleRepository',
          'userpermissionrepository' => 'App\\Domain\\Authorization\\UserPermissionRepository',
          'datetimeimmutable' => 'DateTimeImmutable',
        ),
         'className' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserHandler',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '3db38e0ab38749a239f619de22fd21b0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser',
         'uses' => 
        array (
          'command' => 'App\\Contract\\Command\\Command',
          'commandhandler' => 'App\\Contract\\Command\\CommandHandler',
          'eventcollector' => 'App\\Contract\\Event\\EventCollector',
          'roleassignedtouser' => 'App\\Domain\\Authorization\\Event\\RoleAssignedToUser',
          'duplicateroleassignmentexception' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
          'rolenotfoundexception' => 'App\\Domain\\Authorization\\Exception\\RoleNotFoundException',
          'role' => 'App\\Domain\\Authorization\\Role',
          'roleid' => 'App\\Domain\\Authorization\\RoleId',
          'rolerepository' => 'App\\Domain\\Authorization\\RoleRepository',
          'userpermissionrepository' => 'App\\Domain\\Authorization\\UserPermissionRepository',
          'datetimeimmutable' => 'DateTimeImmutable',
        ),
         'className' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserHandler',
         'functionName' => '__construct',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser',
           'uses' => 
          array (
            'command' => 'App\\Contract\\Command\\Command',
            'commandhandler' => 'App\\Contract\\Command\\CommandHandler',
            'eventcollector' => 'App\\Contract\\Event\\EventCollector',
            'roleassignedtouser' => 'App\\Domain\\Authorization\\Event\\RoleAssignedToUser',
            'duplicateroleassignmentexception' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
            'rolenotfoundexception' => 'App\\Domain\\Authorization\\Exception\\RoleNotFoundException',
            'role' => 'App\\Domain\\Authorization\\Role',
            'roleid' => 'App\\Domain\\Authorization\\RoleId',
            'rolerepository' => 'App\\Domain\\Authorization\\RoleRepository',
            'userpermissionrepository' => 'App\\Domain\\Authorization\\UserPermissionRepository',
            'datetimeimmutable' => 'DateTimeImmutable',
          ),
           'className' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserHandler',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '72b20126b2bb42c4439c2831b391f0ea' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser',
         'uses' => 
        array (
          'command' => 'App\\Contract\\Command\\Command',
          'commandhandler' => 'App\\Contract\\Command\\CommandHandler',
          'eventcollector' => 'App\\Contract\\Event\\EventCollector',
          'roleassignedtouser' => 'App\\Domain\\Authorization\\Event\\RoleAssignedToUser',
          'duplicateroleassignmentexception' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
          'rolenotfoundexception' => 'App\\Domain\\Authorization\\Exception\\RoleNotFoundException',
          'role' => 'App\\Domain\\Authorization\\Role',
          'roleid' => 'App\\Domain\\Authorization\\RoleId',
          'rolerepository' => 'App\\Domain\\Authorization\\RoleRepository',
          'userpermissionrepository' => 'App\\Domain\\Authorization\\UserPermissionRepository',
          'datetimeimmutable' => 'DateTimeImmutable',
        ),
         'className' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserHandler',
         'functionName' => 'handle',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
           'namespace' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser',
           'uses' => 
          array (
            'command' => 'App\\Contract\\Command\\Command',
            'commandhandler' => 'App\\Contract\\Command\\CommandHandler',
            'eventcollector' => 'App\\Contract\\Event\\EventCollector',
            'roleassignedtouser' => 'App\\Domain\\Authorization\\Event\\RoleAssignedToUser',
            'duplicateroleassignmentexception' => 'App\\Domain\\Authorization\\Exception\\DuplicateRoleAssignmentException',
            'rolenotfoundexception' => 'App\\Domain\\Authorization\\Exception\\RoleNotFoundException',
            'role' => 'App\\Domain\\Authorization\\Role',
            'roleid' => 'App\\Domain\\Authorization\\RoleId',
            'rolerepository' => 'App\\Domain\\Authorization\\RoleRepository',
            'userpermissionrepository' => 'App\\Domain\\Authorization\\UserPermissionRepository',
            'datetimeimmutable' => 'DateTimeImmutable',
          ),
           'className' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserHandler',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
    ),
    1 => 
    array (
      '/var/www/html/app/Domain/Authorization/Command/AssignRoleToUser/AssignRoleToUserHandler.php' => '572c317a225901a2929ba8849f10f8701dee4b287e08be331357fd500576052c',
    ),
  ),
));