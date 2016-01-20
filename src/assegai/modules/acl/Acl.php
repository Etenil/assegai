<?php

namespace assegai\modules\acl;

use \assegai\modules;

/**
 * @parents assegai.module.acl
 *
 * ACL module for Assegai
 */
class Acl extends modules\Module
{
    protected $main;
    protected $auxiliary;
    
    public function setOptions($options)
    {
        $this->main = new AclCore($options);
    }

    public static function instanciate()
    {
        return true;
    }

    public function loadAuxPerms(array $perms)
    {
        if (!is_object($this->auxiliary)) {
            $this->auxiliary = new AclCore();
        }
        $this->auxiliary->loadPermissions($perms);
    }

    protected function singleRoleAccess($role, $resource, $privilege)
    {
        if (is_object($this->auxiliary)) {
            $perm = $this->auxiliary->isAllowed($role, $resource, $privilege);
            if ($perm != AclCore::ACL_UNDEF) {
                return $perm;
            }
        }
        return $this->main->isAllowed($role, $resource, $privilege) == AclCore::ACL_ALLOWED;
    }
    
    /**
     * Determines whether the given role or roles have the privilege on the resource.
     * @param $role mixed a string or array of strings corresponding to roles
     * @param $resource string the resource being accessed
     * @param $privilege string the privilege attempted on the resource
     * @return boolean TRUE if access is valid, FALSE otherwise.
     */
    public function isAllowed($role, $resource, $privilege)
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->singleRoleAccess($r, $resource, $privilege)) {
                    return true;
                }
            }
            return false;
        } else {
            return $this->singleRoleAccess($role, $resource, $privilege);
        }
    }

    public function deleteAux()
    {
        unset($this->auxiliary);
    }
}
