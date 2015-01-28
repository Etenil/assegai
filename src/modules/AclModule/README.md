ACL MODULE FOR ASSEGAI
============================

This module provides simple *Access Control Lists* support for Assegai.

The lists must be declared within the application's *conf.php* file. Control lists are in fact action permissions for some roles onto resources. Roles and resources must be defined first, and then their interactions. Below is a succint example of such a list.

```` php
$app['acl'] = array(
    'roles' => array(
        'user' => null,
        ),
    'resources' => array(
        'article' => null,
        ),
    'privileges' => array(
        'user' => array(
            'article' => array('view', 'comment'),
        ),
    ),
);
````

Roles and resources also support inheritance. See below for an example.

```` php
$app['acl'] = array(
    'roles' => array(
        'user' => null,
        'author' => array('user'),
        ),
    'resources' => array(
        'article' => null,
        'admin' => array('article'),
        ),
    'privileges' => array(
        'user' => array(
            'article' => array('view', 'comment'),
        ),
        'admin' => array(
            'article' => array('edit'),
            'admin' => array('access'),
        ),
    ),
);
````

Once the lists are established, you can use the module's *isAllowed()* helper to find if a role is allowed to perform an action on a resource. Below is a small example of this.

```` php
    $article = new Article();
    $user = new User();

    if($this->modules->acl->isAllowed('user', 'article', 'view')) {
        $article->show();
    }
````
