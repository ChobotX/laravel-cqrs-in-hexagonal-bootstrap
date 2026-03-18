<?php declare(strict_types = 1);

// ftm-/var/www/html/app/Presentation/Http/Controller/Web/User/UpdateUserController.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v4-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      '6ea31d7380627cf8bf5898e6fb3428b2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
         'uses' => 
        array (
          'requirespermission' => 'App\\Application\\Authorization\\RequiresPermission',
          'commandbus' => 'App\\Application\\Bus\\CommandBus',
          'querybus' => 'App\\Application\\Bus\\QueryBus',
          'authenticateduser' => 'App\\Contract\\Auth\\AuthenticatedUser',
          'authorizationchecker' => 'App\\Contract\\Authorization\\AuthorizationChecker',
          'organizationcontext' => 'App\\Contract\\Organization\\OrganizationContext',
          'assignroletousercommand' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserCommand',
          'revokerolefromusercommand' => 'App\\Domain\\Authorization\\Command\\RevokeRoleFromUser\\RevokeRoleFromUserCommand',
          'geteffectivepermissionsquery' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
          'getuserrolesquery' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
          'listrolesquery' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
          'role' => 'App\\Domain\\Authorization\\Role',
          'roleassignmentpolicy' => 'App\\Domain\\Authorization\\RoleAssignmentPolicy',
          'setpasswordcommand' => 'App\\Domain\\User\\Command\\SetPassword\\SetPasswordCommand',
          'updateuserrequest' => 'App\\Presentation\\Http\\Request\\Web\\User\\UpdateUserRequest',
          'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
        ),
         'className' => 'App\\Presentation\\Http\\Controller\\Web\\User\\UpdateUserController',
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
      '987d13c1b56c2a410b2ccb3f6eb21c13' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
         'uses' => 
        array (
          'requirespermission' => 'App\\Application\\Authorization\\RequiresPermission',
          'commandbus' => 'App\\Application\\Bus\\CommandBus',
          'querybus' => 'App\\Application\\Bus\\QueryBus',
          'authenticateduser' => 'App\\Contract\\Auth\\AuthenticatedUser',
          'authorizationchecker' => 'App\\Contract\\Authorization\\AuthorizationChecker',
          'organizationcontext' => 'App\\Contract\\Organization\\OrganizationContext',
          'assignroletousercommand' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserCommand',
          'revokerolefromusercommand' => 'App\\Domain\\Authorization\\Command\\RevokeRoleFromUser\\RevokeRoleFromUserCommand',
          'geteffectivepermissionsquery' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
          'getuserrolesquery' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
          'listrolesquery' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
          'role' => 'App\\Domain\\Authorization\\Role',
          'roleassignmentpolicy' => 'App\\Domain\\Authorization\\RoleAssignmentPolicy',
          'setpasswordcommand' => 'App\\Domain\\User\\Command\\SetPassword\\SetPasswordCommand',
          'updateuserrequest' => 'App\\Presentation\\Http\\Request\\Web\\User\\UpdateUserRequest',
          'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
        ),
         'className' => 'App\\Presentation\\Http\\Controller\\Web\\User\\UpdateUserController',
         'functionName' => '__construct',
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
      '66b2c07e49a83cad7e2af295f3c37329' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
         'uses' => 
        array (
          'requirespermission' => 'App\\Application\\Authorization\\RequiresPermission',
          'commandbus' => 'App\\Application\\Bus\\CommandBus',
          'querybus' => 'App\\Application\\Bus\\QueryBus',
          'authenticateduser' => 'App\\Contract\\Auth\\AuthenticatedUser',
          'authorizationchecker' => 'App\\Contract\\Authorization\\AuthorizationChecker',
          'organizationcontext' => 'App\\Contract\\Organization\\OrganizationContext',
          'assignroletousercommand' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserCommand',
          'revokerolefromusercommand' => 'App\\Domain\\Authorization\\Command\\RevokeRoleFromUser\\RevokeRoleFromUserCommand',
          'geteffectivepermissionsquery' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
          'getuserrolesquery' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
          'listrolesquery' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
          'role' => 'App\\Domain\\Authorization\\Role',
          'roleassignmentpolicy' => 'App\\Domain\\Authorization\\RoleAssignmentPolicy',
          'setpasswordcommand' => 'App\\Domain\\User\\Command\\SetPassword\\SetPasswordCommand',
          'updateuserrequest' => 'App\\Presentation\\Http\\Request\\Web\\User\\UpdateUserRequest',
          'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
        ),
         'className' => 'App\\Presentation\\Http\\Controller\\Web\\User\\UpdateUserController',
         'functionName' => '__invoke',
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
      'c8498d95cbe7d80860cb387ee2811f89' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
         'uses' => 
        array (
          'requirespermission' => 'App\\Application\\Authorization\\RequiresPermission',
          'commandbus' => 'App\\Application\\Bus\\CommandBus',
          'querybus' => 'App\\Application\\Bus\\QueryBus',
          'authenticateduser' => 'App\\Contract\\Auth\\AuthenticatedUser',
          'authorizationchecker' => 'App\\Contract\\Authorization\\AuthorizationChecker',
          'organizationcontext' => 'App\\Contract\\Organization\\OrganizationContext',
          'assignroletousercommand' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserCommand',
          'revokerolefromusercommand' => 'App\\Domain\\Authorization\\Command\\RevokeRoleFromUser\\RevokeRoleFromUserCommand',
          'geteffectivepermissionsquery' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
          'getuserrolesquery' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
          'listrolesquery' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
          'role' => 'App\\Domain\\Authorization\\Role',
          'roleassignmentpolicy' => 'App\\Domain\\Authorization\\RoleAssignmentPolicy',
          'setpasswordcommand' => 'App\\Domain\\User\\Command\\SetPassword\\SetPasswordCommand',
          'updateuserrequest' => 'App\\Presentation\\Http\\Request\\Web\\User\\UpdateUserRequest',
          'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
        ),
         'className' => 'App\\Presentation\\Http\\Controller\\Web\\User\\UpdateUserController',
         'functionName' => 'syncRoles',
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
      '4e6256a358769444ccd33a4b236b876e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Presentation\\Http\\Controller\\Web\\User',
         'uses' => 
        array (
          'requirespermission' => 'App\\Application\\Authorization\\RequiresPermission',
          'commandbus' => 'App\\Application\\Bus\\CommandBus',
          'querybus' => 'App\\Application\\Bus\\QueryBus',
          'authenticateduser' => 'App\\Contract\\Auth\\AuthenticatedUser',
          'authorizationchecker' => 'App\\Contract\\Authorization\\AuthorizationChecker',
          'organizationcontext' => 'App\\Contract\\Organization\\OrganizationContext',
          'assignroletousercommand' => 'App\\Domain\\Authorization\\Command\\AssignRoleToUser\\AssignRoleToUserCommand',
          'revokerolefromusercommand' => 'App\\Domain\\Authorization\\Command\\RevokeRoleFromUser\\RevokeRoleFromUserCommand',
          'geteffectivepermissionsquery' => 'App\\Domain\\Authorization\\Query\\GetEffectivePermissions\\GetEffectivePermissionsQuery',
          'getuserrolesquery' => 'App\\Domain\\Authorization\\Query\\GetUserRoles\\GetUserRolesQuery',
          'listrolesquery' => 'App\\Domain\\Authorization\\Query\\ListRoles\\ListRolesQuery',
          'role' => 'App\\Domain\\Authorization\\Role',
          'roleassignmentpolicy' => 'App\\Domain\\Authorization\\RoleAssignmentPolicy',
          'setpasswordcommand' => 'App\\Domain\\User\\Command\\SetPassword\\SetPasswordCommand',
          'updateuserrequest' => 'App\\Presentation\\Http\\Request\\Web\\User\\UpdateUserRequest',
          'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
        ),
         'className' => 'App\\Presentation\\Http\\Controller\\Web\\User\\UpdateUserController',
         'functionName' => 'availableModules',
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
    ),
    1 => 
    array (
      '/var/www/html/app/Presentation/Http/Controller/Web/User/UpdateUserController.php' => '8ec783ea7a51d5887445a03423aee17344bfbdd673f17e4a938b2cd1c177b36b',
    ),
  ),
));