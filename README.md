Assegai
=======

VERSION 0.1
UPDATED 15 August 2012

Introduction
------------
Assegai is a full-featured MVC framework for PHP. It is free software under the GPLv3 license.

The framework relies on the tiny micro-framework *Atlatl* for low-level tasks and provides wrappers to *Atlatl*'s features.

Installation
------------
To install Assegai, you will either need to retrieve the latest package from the [official website](http://assegai.etenil.net), or from the project's [Mercurial repository](http://pikacode.com/etenil/assegai).

Decompress the package or clone the repository on your web server. Your document root should point to the *public* folder of assegai.

Naming Convention
-----------------
Assegai encourages the use of multiple specialised apps that can share models in order to implement websites. Based on this, the controllers and models follow a particular naming convention that lets Assegai's auto-loader find and require the correct files.

Classes need to be named like so:

    <App>_<Type>_<Name>

For instance:

    Myapp_Model_TestCode


Basic Configuration
-------------------
Even though Assegai has default configuration options, it will not work unless a configuration file is created. Simply create the file *conf.php* in the framework's root.

You will need to inform Assegai of the section or URL that precedes the first url segment. This means that if you need to access Assegai through the url *http://example.org/assegai/public/index.php*, then your configuration file should contain the following code:

    <?php
    $conf['prefix'] = /assegai/public/index.php';

If you can access Assegai through *http://example.org/index.php*, you'll still need to add the '/index.php' prefix. The only case where you don't need to specify the prefix is if you have set up your server to rewrite urls and get rid of the */index.php* part.

Hello World
-----------
In this chapter, we'll see how to write a very simple application for assegai, it will simply display the famous "Hello, World" message on an HTML page.

After having configured the framework as explained previously, run the following command:

    ./assegai app hello

This will create the file-system tree for your application. The default application tree is like so:

    apps
    `- hello
       |- conf.php
       |- constrollers
       |- models
       `- views

Let's write a controller first. Controllers execute actions and coordinate models and views in order to produce the desired output. Create the file *hello.php* within the *controllers* directory and put the following code:

    class Hello_Controller_Test extends assegai\Controller
    {
        function hello()
        {
            return "Hello, World!";
        }
    }

We still need to indicate to the framework that this controller needs to be called when visiting the website. This is done by adding the following contents to the application's *conf.php* file.

    $conf['route'] = array(
         '/' => 'Hello_Controller_Hello::hello',
         );

Now you can visit your web server and should see the *Hello, World* message printed.

### Using a model
Let us now try and modify the exercise by introducing a model. Models are a powerful and convenient way to organise your code, by delegating all data management to a dedicated class.

Create the file *models/hello.php* that will contain the following code:

    class Hello_Model_Hello extends assegai\Model
    {
        function hello()
        {
            return 'Hello, Model';
        }
    }

We will need to load the model from the controller now. Let's create a new function in *controllers/hello.php*:

    class Hello_Controller_Test extends assegai\Controller
    {
        function hello()
        {
            return "Hello, World!";
        }

        function hello_model()
        {
            $hello = $this->model('Model_Hello');
            return $hello->hello();
        }
    }

Finally, we need to create a route to this new function in conf.php like so:

    $app['route'] = array(
        '/' => 'Hello_Controller_Hello::hello',
        '/model' => 'Hello_Controller_Hello::hello_model',
        );

Now try visiting your installation with the segment */model* e.g. http://localhost/index.php/model. You should see the message "Hello, Model" displayed.

### Views
Now let's try doing the same thing we did before but by using a view instead. We'll fetch the data from our existing model, then feed it into a view and display this.

We will create the view first. Create the file views/hello.phtml with the following code:

    <DOCTYPE html>
    <html>
        <head>
            <title>Assegai Tutorial</title>
        </head>
        <body>
            <p><?=$vars->message?></p>
        </body>
    </html>

Notice the *$vars->message* variable.

Let's create another function within the controller's body like so:

    function hello_view()
    {
        $hello = $this->model('Hello_Model_Hello');
        return $this->view('hello', array('message' => $hello->hello()));
    }

Finally we will create a route to this new function in conf.php:

    $app['route'] = array(
        '/' => 'Hello_Controller_Hello::hello',
        '/model' => 'Hello_Controller_Hello::hello2',
        '/view' => 'Hello_Controller_Hello::hello_view',
    );

Try visiting the url with the segment */view*, for instance http://localhost/index.php/view and you should see the view with *Hello, Model* in place of the message variable.

Routing
-------
Assegai makes an extended use of *Atlatl*'s routing features. Routes are defined on an per-application basis and conflicting routes are overwritten by applications loaded later.

Routes are regex-based. Thus it is easy to wildcard any part of a route and direct to the same handler. Capturing braces within a route are mapped as parameters to the handler. Thus one could use the following:

    // Handler for route '/foo/([0-9]+)'
    function foo($num)
    {
        return $num;
    }

Routes can also be defined for a specific HTTP method by prepending the desired method with columns to the route like so:

    $app['route'] = array(
        'GET:/bar' => 'Controller_Foo::bar_get',
        'POST:/bar' => 'Controller_Foo::bar_post',
        '/bar' => 'Controller_Foo::bar',
    );

The routes table is searched for method-specific routes first, then for generic routes if none is found.

Controllers
-----------
Controllers are the heart of an application in Assegai. They provide access to models, views and modules.

### Initialisation
Assegai provides a basic implementation of its controllers that your controllers should extend. This implementation contains a final constructor that cannot be overloaded. You should therefore put your initialisation code within the provided *_init()* method, that is always called by the parent constructor.

### Views
The controller contains the helper method *view()* which loads a view and returns the populated contents. The *view()* helper takes the view's name as argument and an associative array of values as second argument.

### Models
Controllers have the *model()* helper function to easily load a model. This takes the model's class name as parameter and returns the instanciated model.

### Modules
Modules are loaded along with the application and the member variable *modules* provides easy access to those from a controller. See the dedicated chapter for more information.

### Other helpers
The *dump()* method allows to easily dump a value wrapped into a *pre* HTML block, thus making it readable.

The *appPath()* method can be used to get the absolute path to an application-relative path. The method expects a relative path as parameter.

Models
------
Models are objects providing abstraction to some data provider. Their role is typically to ensure data validity, storage and retrieval.

Assegai leaves you free to organise your models however you like, the only method provided with the base *Model* class is *_init()*, and the member variable *$modules* to access the loaded modules.

Unit Tests
----------
Assegai supports unit testing your applications and provides the necessary glue code to have PHPUnit run nicely.

In order to test your code, you'll need to have PHPUnit installed and to create a *phpunit.xml* and a bootstrap file within the tests folder.

The *phpunit.xml* is typically like so:

    <phpunit bootstrap="./bootstrap.php" colors="true">
        <testsuite name="Unit">
            <directory>./</directory>
        </testsuite>
    </phpunit>

And the bootstrap code written in a *bootstrap.php* file like so:

    <?php
    define('APP_PATH', dirname(__DIR__) . '/');
    define('ROOT_PATH', dirname(dirname(dirname(__DIR__))) . '/');
    require(ROOT_PATH . 'lib/testloader.php');

You might need to update the definitions of APP_PATH and ROOT_PATH so as to match your real settings.

You can then run your tests with the command:

    phpunit -c phpunit.xml

Modules
-------
The modules from *Atlatl* are extended by *Assegai*. They provide access to advanced features either from the provided *$modules* helper variable in the *Controller* or *Model*, or from actions on hooks to modify the framework's behaviour.

Assegai comes with several modules pre-installed. The following sections will describe each of those.

### ACL
This module provides simple *Access Control Lists* support for Assegai.

The lists must be declared within the application's *conf.php* file. Control lists are in fact action permissions for some roles onto resources. Roles and resources must be defined first, and then their interactions. Below is a succint example of such a list.

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

Roles and resources also support inheritance. See below for an example.

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


Once the lists are established, you can use the module's *isAllowed()* helper to find if a role is allowed to perform an action on a resource. Below is a small example of this.

    $article = new Article();
    $user = new User();

    if($this->modules->acl->isAllowed('user', 'article', 'view')) {
        $article->show();
    }

### Mustache
This module replaces the standard views with [Mustache](http://mustache.github.com). Beware, this module expects the view files to have the extension *.tpl* instead of the usual *.phtml*.

For more information on the template engine's syntax, look at the official documentation on the project's website.

### PDO
This provides abstraction for PDO database connections.

Database connections must be defined within the application's *conf.php* file like so:

    $app['pdo'] = array(
        'myNiceConnection' => array(
            'dsn' => 'mysql:host=localhost;dbname=somedb',
            'username' => 'root',
            'password' => 'somepassword!',
        ),
        'conn2' => array(
            'dsn' => 'mysql:host=localhost;dbname=otherdb',
            'username' => 'root',
            'password' => 'somepassword!',
        ),
    );

This will instanciate two connections that can be accessed like so:

    $this->modules->pdo->myNiceConnection->exec('INSERT INTO test VALUES('1', '2', '3')');

Check out the PHP documentation for PDO for more information about the connections.

### Paginator
This module provides a convenient paginator for results. The module currently only supports paginating arrays.

The following functions are provided:

- getPage($num) Gets the page $num
- getCurrentPage() Returns the current page
- count() Counts the number of elements.
- setPage($num) Sets the paginator to page $num
- setPageLength($length) Sets the page's length to $length. Default is 10.
- getPageNum() Gets the current page's number
- getPages() Returns the total number of pages available
- getPagesList($length) Gets the list of pages surrounding the current one as an array

Example of usage:

    $data = range(0, 100);
    $paginator = new Module_Paginator::fromArray($data);
    $paginator->setPage(3);
    foreach($paginator->getCurrentPage() as $item) {
        echo $item;
    }
    foreach($paginator->getPagesList() as $page) {
        echo $page;
    }

### ESI
The ESI module brings ESI-compliance to *Assegai*. ESI is a standard by which fragments of a page can be cached separately and the final page reconstituded by an edge service. ESI is for instance implemented in Squid.

This module doesn't really work yet. You're welcome to work on it if you have some time and motivation.
