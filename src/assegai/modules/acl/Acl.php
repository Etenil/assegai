<?php

namespace assegai\modules\acl
{
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
        
        function setOptions($options)
        {
            $this->main = new AclCore($options);
        }

        public static function dependencies()
        {
            return array(
                array(
                    'name' => 'module_acl',
                    'class' => '\\assegai\\modules\\acl\\Acl',
                    'mother' => 'module',
                ),
            );
        }

        public static function instanciate()
        {
            return true;
        }

        public function loadAuxPerms(array $perms) {
            if(!is_object($this->auxiliary)) {
                $this->auxiliary = new AclCore();
            }
            $this->auxiliary->loadPermissions($perms);
        }

        public function isAllowed($role, $resource, $privilege) {
            if(is_object($this->auxiliary)) {
                $perm = $this->auxiliary->isAllowed($role, $resource, $privilege);
                if($perm != AclCore::ACL_UNDEF) {
                    return $perm;
                }
            }
            return $this->main->isAllowed($role, $resource, $privilege) == AclCore::ACL_ALLOWED;
        }

        public function deleteAux() {
            unset($this->auxiliary);
        }
    }
}
