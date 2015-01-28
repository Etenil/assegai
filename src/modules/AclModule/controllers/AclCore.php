<?php

namespace etenil\modules\AclModule\controllers;

/**
 * Core ACL validation framework.
 *
 * Configuration example:
 *
 * $app['modules'] = array('acl');
 *
 * $app['acl'] = array(
 *    'roles' => array(
 *        'guest' => null,
 *        'user'  => array('guest'),
 *        'editor'=> array('user'),
 *        'admin' => array('guest', 'user'),
 *        ),
 *    'resources' => array(
 *        'news'  => null,
 *        'story' => array('news'),
 *        ),
 *    'privileges' => array(
 *        'guest' => array(
 *            'news' => array('view'),
 *            ),
 *        'user' => array(
 *            'news' => array('edit', 'publish'),
 *            ),
 *        'editor' => array(
 *            'news' => array('!edit', 'delete'), // Deny edit.
 *            ),
 *        'admin' => array(
 *            'news' => array('view', 'edit', 'publish', 'delete'),
 *            'story' => array('view', 'edit', 'publish', 'delete'),
 *            ),
 *        ),
 *    );
 */
class AclCore {
    protected $roles;
    protected $resources;
    protected $privileges;

    /** Denied ACL */
    const ACL_DENIED = 0;
    /** Allowed ACL */
    const ACL_ALLOWED = 1;
    /** Undefined ACL, defaults to DENIED */
    const ACL_UNDEF = 2;

    function __construct(array $perms = null) {
        if($perms) {
            $this->loadPermissions($perms);
        }
    }

    function loadPermissions(array $perms) {
        if(!isset($perms['roles'])) {
            throw new exceptions\AclTreeParse("Roles aren't defined.");
        }
        if(!isset($perms['resources'])) {
            throw new exceptions\AclTreeParse("Resources aren't defined.");
        }
        if(!isset($perms['privileges'])) {
            throw new exceptions\AclTreeParse("Privileges aren't defined.");
        }

        $this->roles      = $perms['roles'];
        $this->resources  = $perms['resources'];
        $this->privileges = $perms['privileges'];
    }

    protected function isAllowedSingle($role, $resource, $privilege)
    {
        if(isset($this->privileges[$role])
           && isset($this->privileges[$role][$resource])
           && in_array('!' . $privilege, $this->privileges[$role][$resource])) {
            return self::ACL_DENIED;
        }
        elseif(isset($this->privileges[$role])
               && isset($this->privileges[$role][$resource])
               && in_array($privilege, $this->privileges[$role][$resource])) {
            return self::ACL_ALLOWED;
        }

        return self::ACL_UNDEF;
    }

    protected function isAllowedDig($role, $resource, $privilege, $reinsert = true)
    {
        $roles = is_array($this->roles[$role])? $this->roles[$role] : array();
        $resources = is_array($this->resources[$resource])? $this->resources[$resource] : array();

        if($reinsert) {
            $roles[] = $role;
            $resources[] = $resource;
        }

        for($res_n = count($resources) - 1; $res_n >= 0; $res_n--) {
            for($role_n = count($roles) - 1; $role_n >= 0; $role_n--) {
                $perm = $this->isAllowedSingle($roles[$role_n], $resources[$res_n], $privilege);
                if($perm != self::ACL_UNDEF) {
                    return $perm;
                }
                elseif(($role_n < count($roles) - 1 || $res_n < count($resources) - 1)) {
                    $perm = $this->isAllowedDig($roles[$role_n], $resources[$res_n],
                                                $privilege, true);
                    if($perm != self::ACL_UNDEF) {
                        return $perm;
                    }
                }
            }
        }

        return self::ACL_UNDEF;
    }

    function isAllowed($role, $resource, $privilege)
    {
        // Clearing the terrain.
        if(!in_array($role, array_keys($this->roles))) {
            throw new exceptions\UndefinedRole("Role `$role' does not exist");
        }

        if(!in_array($resource, array_keys($this->resources))) {
            throw new exceptions\UndefinedResource("Resource `$resource' does not exist");
        }

        // Doing a tree look-up for the specified role and resource (no parents)
        $perm = $this->isAllowedSingle($role, $resource, $privilege);
        if($perm == self::ACL_UNDEF) { // Digging the privilege tree.
            $perm = $this->isAllowedDig($role, $resource, $privilege);
        }

        return $perm;
    }
}
