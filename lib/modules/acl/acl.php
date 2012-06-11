<?php


class Module_Acl extends \assegai\Module
{
    protected $acl;

    function _init($options)
    {
        $this->acl = $options;
    }

    function isAllowed($role, $resource, $privilege)
    {
        // TODO
    }
}
