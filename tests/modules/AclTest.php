<?php

require_once('../lib/modules/acl/acl.php');

class AclTest extends \centrifuge\Test
{
    protected $acl;

    function init() {
        $this->acl = new AclCore(array(
            'roles' => array(
                'guest' => null,
                'user'  => array('guest'),
                'editor'=> array('user'),
                'admin' => array('guest', 'user'),
            ),
            'resources' => array(
                'news'  => null,
                'story' => array('news'),
            ),
            'privileges' => array(
                'guest' => array(
                    'news' => array('view'),
                ),
                'user' => array(
                    'news' => array('edit', 'publish'),
                ),
                'editor' => array(
                    'news' => array('!edit', 'delete'), // Deny edit.
                ),
                'admin' => array(
                    'news' => array('view', 'edit', 'publish', 'delete'),
                    'story' => array('view', 'edit', 'publish', 'delete'),
                ),
            ),
        ));
    }
    
    public function testAllowed() {
        $this->equals(AclCore::ACL_ALLOWED, $this->acl->isAllowed('guest', 'news', 'view'));
        $this->equals(AclCore::ACL_ALLOWED, $this->acl->isAllowed('editor', 'news', 'delete'));
    }

    public function testDisallowed() {
        $this->equals(AclCore::ACL_UNDEF, $this->acl->isAllowed('guest', 'news', 'edit'));
        $this->equals(AclCore::ACL_DENIED, $this->acl->isAllowed('editor', 'news', 'edit'));
    }
}

