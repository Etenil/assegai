<?php

/**
 * ACL module for Assegai
 *
 * Configuration example:

 $app['modules'] = array('acl');

 $app['acl'] = array(
    'roles' => array(
        'guest' => null,
        'user'  => array('guest'),
        'admin' => array('guest', 'user'),
        ),
    'resources' => array(
        'news'  => null,
        'story' => array('news'),
        ),
    'privileges' => array(
        'guest' => array(
            array('news', 'view'),
            ),
        'user' => array(
            array('news', array('edit', 'publish')),
            ),
        'admin' => array(
            array('news', array('view', 'edit', 'publish', 'delete')),
            array('story', array('view', 'edit', 'publish', 'delete')),
            ),
        ),
    );
*/

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
