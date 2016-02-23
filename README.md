# ASSEGAI

UPDATED 03 August 2014

## Introduction

Assegai is a full-featured MVC framework for PHP. It is free software under the MIT license.

The framework used to rely on the micro-framework [Atlatl](http://github.com/Etenil/atlatl). This dependency was dropped in version 2.0.

## Installation

*Note: A full demonstration video is available illustrating points covered in the installation and the getting started sections. Please visit [youtube](https://www.youtube.com/watch?v=c3jxjB5p99E) to view the video.*

To install Assegai you will first need to install [composer](http://getcomposer.org). Then create a composer.json file for your project that requires *etenil/assegai*. An example composer.json file should look as following:
```json
{
    "name": “YOUR_NAME/helloworld”,
    "require": {
        "etenil/assegai": "2.*"
    }
}
```

Now save and exit the composer.json file and from your project’s root folder (using command line) run the following command:  

    $ composer install

Once the installation of Assegai is completed, you will have a fully functional MVC framework installed inside your project's folder. 

## Getting started

There are many possible ways to setup and use the Assegai framework. Following sections demonstrates currently the most simple setup. The recommended setup documentation will follow shortly.

### Bootstrapping

You will need to make a bootstrapper or use the default one for your project. The framework comes with an example bootstrapper that you can adapt. 

To utilise the default bootstrapper run the following command from within your project’s root folder: 

    $ cp vendor/etenil/assegai/bootstrapper.example.php index.php

This will copy the already provided example bootstrapper file into the project’s root folder, renaming the file to *index.php*. 

### Basic Configuration

Even though Assegai has default configuration options, it will not work unless a configuration file is created, even if empty. You can simply use the file *conf.example.php* that comes with the framework. To do that from your project root folder use the following command:

    $ cp vendor/etenil/assegai/conf.example.php conf.php

Now you have fully configured framework. In order to test the setup the flowing section will demonstrate how to create an application with Assegai. 

### Hello World app

In this chapter, we'll see how to write a very simple application for assegai, it will simply display the famous "Hello, World" message on an HTML page.

To create application firstly go into the conf.php file in your projects’s root folder and add the application name into the apps array. 
Feel free to delete or simply rename the currently existing sample app.

Your configuration file should look as following: 
```php
<?php
    $conf['apps_path'] = __DIR__ . '/apps';

    $conf['apps'] = [
        'helloworld',   
    ];
```

Now lets create the actual application. From the project’s root folder type in following command:
    
    $ vendor/etenil/assegai/assegai app helloworld

This will create the file-system tree for your application. The default application tree is like so:

    apps
     - helloworld
       |- conf.php
       |- controllers
       |- exceptions
       |- models
       |- views


Now create the file *Hello.php* within the *controllers* directory and put the following code:
```php
<?php
    namespace helloworld\controllers;
    
    class Hello extends \assegai\Controller
    {
        function sayHello()
        {
            return "Hello, World!";
        }
    }
```
Be careful to put the first backslash on *\assegai\Controller*, otherwise you'll have issues.

We still need to indicate to the framework that this controller needs to be called when visiting the website. This is done by adding the following contents to the application's *conf.php* file. Note that this conf.php file is different to the general configuration file. 
```php
<?php
    $app['route'] = [
        '/' => 'helloworld\controllers\Hello::sayHello',
    ];
```

### Test it

Now, start the PHP internal web server with (or configure the your preferred web server):

php -S localhost:8080 -t ./

And then, when visit your web server with a browser you should see the *Hello, World* message printed.


### Namespacing Convention

Assegai encourages the use of multiple specialised apps that can share models in order to implement websites. The framework relies on the [PSR-0](http://www.php-fig.org/psr/psr-0/) naming standard.

Classes need to be named like so:

    app\type\Name

For instance:

    myapp\models\DemoCode

You can also use sub-folders for models and controllers and extend the namespace accordingly:

    myapp\models\demo\SomeModel



#### Using a model
Let us now try and modify the exercise by introducing a model. Models are a powerful and convenient way to organise your code, by delegating all data management to a dedicated class.

Create the file *models/hello.php* that will contain the following code:

    namespace hello\models;
    
    class Hello extends \assegai\Model
    {
        function hello()
        {
            return 'Hello, Model';
        }
    }

We will need to load the model from the controller now. Let's create a new function in *controllers/hello.php*:

    namespace hello\controllers;
    
    class Demo extends \assegai\Controller
    {
        function hello()
        {
            return "Hello, World!";
        }

        function hello_model()
        {
            $hello = $this->model('Hello');
            return $hello->hello();
        }
    }

Finally, we need to create a route to this new function in conf.php like so:

    $app['route'] = [
        '/' => 'hello\controllers\Demo::hello',
        '/model' => 'Demo::hello_model',
        ];

Now try visiting your installation with the segment */model* e.g. http://localhost/index.php/model. You should see the message "Hello, Model" displayed.

Note that we used an implicit namespace route for the '/model' path. This is handy and saves typing.

#### Views
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
        $hello = $this->model('Hello');
        return $this->view('hello', array('message' => $hello->hello()));
    }

Finally we will create a route to this new function in conf.php:

    $app['route'] = [
        '/' => 'Demo::hello',
        '/model' => 'Demo::hello2',
        '/view' => 'Demo::hello_view',
    ];

Try visiting the url with the segment */view*, for instance http://localhost/index.php/view and you should see the view with *Hello, Model* in place of the message variable.

##### View helpers
View helpers are convenient functions that return or output some HTML and are used to format and display data within the view. Consider the following:

    <?php $h->form->input('text', 'foobar') ?>
    => <input type="text" name="foobar" id="foobar"/>

You might think of implementing this function like this:

    function input($type, $name) {
        echo "<input type=\"$type\" name=\"$name\" id=\"$name\"/>";
    }

Helpers are not loaded by default; the view must declare the necessary helpers that it uses like this:

    <?php $load_helper('form'); ?>

At the moment, Assegai does not provide any helper. You can easily implement your own though.

Helpers are always accessible to all your applications and are declared as part of classes within the *helpers_path* folder (by default a folder called *helpers* in the project root).

A helper class must follow the usual convention and be named *helpers\SomeName*. It's a good idea to package related helpers together. Here's an example:

    namespace helpers;
    
    class Form {
        function input($type, $name) {
            echo "<input type=\"$type\" name=\"$name\" id=\"$name\"/>";
        }
    }

## Routing

Routes are defined on an per-application basis and conflicting routes are overwritten by applications loaded later.

Routes are regex-based. Thus it is easy to wildcard any part of a route and direct to the same handler. Capturing braces within a route are mapped as parameters to the handler. Thus one could use the following:

    // Handler for route '/foo/([0-9]+)'
    function foo($num)
    {
        return $num;
    }

Routes can also be defined for a specific HTTP method by prepending the desired method with columns to the route like so:

    $app['route'] = [
        'GET:/bar' => 'app\controllers\Foo::bar_get',
        'POST:/bar' => 'app\controllers\Foo::bar_post',
        '/bar' => 'app\controllers\Foo::bar',
    ];

Note that routes also support implicit namespacing like so:

    $app['route'] = [
        '/bar' => 'Foo::bar', // This will be assumed as 'app\controllers\Foo'.
    ];

In large routing tables, it is convenient to use route groups. They make the table clearer and reduce typing:

    $app['route'] = [
        '@/test' => [
            '/foo' => 'Test::testFoo', // This is understood as '/test/foo'.
            '/bar' => 'Test::testBar',
        ],
        '@/real' => [
            '/foo' => 'Real::foo',
            '/bar' => 'Real::bar',
        ],
    ];

The route table is searched for method-specific routes first, then for other routes if none is found.

#### URL Prefix

If your website runs from a subfolder, then the routes end up having a constant prefix to them e.g. the site will run in the `foo` folder and result in a URL like `http://host.com/foo/myroute`. In this case, set the URL prefix in the configuration like so:

````
$conf['prefix'] = '/foo'
````

Then the route will be automatically resolved as `/myroute`.

### Generic routes

The bundled router features several built-in handlers. Those can be used to perform instant actions on URLs.

Generic routes are defined within the router itself, and do cannot be extended or modified. They accept parameters, and therefore look different than usual routes.

A typical generic route could be as simple as:

    '::access_denied'

Or use parameters as:

    ['::view', 'baz', ['foo' => 'bar']]

#### Redirections
Redirections are just an easy way to achieve 301 HTTP redirections without touching the web server's configuration files, or creating a dedicated controller. The redirect route accepts two parameters, the first being the redirect target, and the second is the HTTP code for the redirect (typically 301 or sometimes 302). The second parameters defaults to 301 if omitted.

    $app['route'] = [
        '/foo' => ['::redirect', '/bar', 301],
        '/bar' => 'app\\controllers\\Bar::bar',
    ];

#### Views
View generic routes are perfect for all the quasi-static files that all websites have. The view route accepts two parameters, the first is the name of the view, and the last is an associative array that is passed to the view.

    $app['route'] = [
        '/foo' => ['::view', 'foo', ['text' => 'Hello, world!']],
        '/bar' => ['::view', 'bar'],
    ];

## Configuration

Framework and application configurations are readily accessible as part of the server property. You can access them like so:

    $this->server->main->get('apps');
    $this->server->app->get('my_very_important_setting');

Those configuration dictionary simply wrap the parsed configuration files, so you can define your own settings easily.

## Controllers

Controllers are the heart of an application in Assegai. They provide access to models, views and modules.

### Initialisation
Assegai provides a basic implementation of its controllers that your controllers should extend. This implementation contains a final constructor that cannot be overloaded. You should therefore put your initialisation code within the provided *_init()* method, that is always called by the parent constructor.

### Views
The controller contains the helper method *view()* which loads a view and returns the populated contents. The *view()* helper takes the view's name as argument and an associative array of values as second argument.

### Models
Controllers have the *model()* helper function to easily load a model. This takes the model's class name as parameter and returns the instanciated model.

The model helper supports implicit namepaces, thus saving a lot of typing:

    class MyController extends \assegai\Controller
    {
        function foo()
        {
            $my_model = $this->model('app\models\Foo');
            $my_short_model = $this->model('Foo');
        }
    }

### Modules
Modules are shared among applications and the member variable *modules* provides easy access to those from a controller. See the dedicated chapter for more information.

### Other utilities
The *dump()* method allows to easily dump a value wrapped into a *pre* HTML block, thus making it readable.

The *appPath()* method can be used to get the absolute path to an application-relative path. The method expects a relative path as parameter.

Models
------
Models are objects providing abstraction to some data provider. Their role is typically to ensure data validity, storage and retrieval.

Assegai leaves you free to organise your models however you like, the only method provided with the base *Model* class is *_init()*, and the member variable *$modules* to access the loaded modules.

### Application models
Each application can have its own models within its *models* folder. These models follow the naming convention

    <application>\models\<Model>

### Shared models
If your website is small enough that you only need one application, then you might want to use models only within your application. However, in case of a non-trivial website, it usually becomes more comfortable to use shared models.

Shared models all reside in a single folder determined by the *models_path* configuration, by default a *models* folder within assegai's root. They can be loaded easily by any application.

Typically, you will end up having quite a few models within your shared folder. Depending on how you organise them, you might have several classes for a single model. For this reason, the naming convention for shared models is a little different, the underscore characters are used as separator to determine where to find the model's source file. This way, you can organise your models within folders.

    models\polls\Negative\Mapper
    models/poll/negative/Mapper.php

## Exceptions

Applications come with an *exceptions* folder. This folder is meant to contain Exception classes that may be thrown anywhere in your application and handled.

The naming convention for exceptions is simply:

    <app>\exceptions\<Name>

## Unit Tests

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

## Modules

The modules provide access to advanced features either from the provided *$modules* helper variable in the *Controller* or *Model*, or from actions on hooks to modify the framework's behaviour.

Modules are globally available in *Assegai*, although you can declare them within applications'configuration as well as the main configuration. If the current application contains configuration for a loaded module, this configuration will be given priority other that of the global configuration file.

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
This module replaces the standard views with a modified [Mustache](http://mustache.github.com) engine. Beware, this module expects the view files to have the extension *.tpl* instead of the usual *.phtml*.

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

    $this->modules->pdo->myNiceConnection->exec('INSERT INTO demo VALUES('1', '2', '3')');

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
    $paginator = new \assegai\modules\paginator\Paginator::fromArray($data);
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

### Validator
The Validator module lets you easily check your form data against a set of formats and filters.

#### A Quick Example

The example below shows how to throw validation exceptions with the custom
exception. You can then retrieve the error messages from the calling method.
It is not good practice to validate your data in your controller, this should
be handled in your Model. This is just a quick example.

    $validator = new \assegai\modules\validator\Validator($post);
    $validator
        ->required('You must supply a name.')
        ->validate('name', 'Name');
    $validator
        ->required('You must supply an email address.')
        ->email('You must supply a valid email address')
        ->validate('email', 'Email');

    // check for errors
    if ($validator->hasErrors()) {
        throw new Validator_Exception(
            'There were errors in your form.',
            $validator->getAllErrors()
            );
    }

#### Available Validation Methods

* exists($message = null) - The field must exist, regardless of it's content.
* required($message = null) - The field value is required.
* email($message = null) - The field value must be a valid email address string.
* float($message = null) - The field value must be a float.
* integer($message = null) - The field value must be an integer.
* digits($message = null) - The field value must be a digit (integer with no upper bounds).
* min($limit, $include = TRUE, $message = null) - The field value must be greater than $limit (numeric). $include defines if the value can be equal to the limit.
* max($limit, $include = TRUE, $message = null) - The field value must be less than $limit (numeric). $include defines if the value can be equal to the limit.
* between($min, $max, $include = TRUE, $message = null) - The field value must be between $min and $max (numeric). $include defines if the value can be equal to $min and $max.
* minLength($length, $message = null) - The field value must be greater than or equal to $length characters.
* maxLength($length, $message = null) - The field value must be less than or equal to $length characters.
* length($length, $message = null) - The field must be $length characters long.
* matches($field, $label, $message = null) - One field matches another one (i.e. password matching)
* notMatches($field, $label, $message = null) - The field value must not match the value of $field.
* startsWith($sub, $message = null) - The field must start with $sub as a string.
* notStartsWith($sub, $message = null) - The field must not start with $sub as a string.
* endsWith($sub, $message = null) - THe field must end with $sub as a string.
* notEndsWith($sub, $message = null) - The field must not end with $sub as a string.
* ip($message = null) - The field value is a valid IP, determined using filter_var.
* url($message = null) - The field value is a valid URL, determined using filter_var.
* date($message = null) - The field value is a valid date, can be of any format accepted by DateTime()
* minDate($date, $format, $message = null) - The date must be greater than $date. $format must be of a format on the page http://php.net/manual/en/datetime.createfromformat.php
* maxDate($date, $format, $message = null) - The date must be less than $date. $format must be of a format on the page http://php.net/manual/en/datetime.createfromformat.php
* ccnum($message = null) - The field value must be a valid credit card number.
* oneOf($allowed, $message = null) - The field value must be one of the $allowed values. $allowed can be either an array or a comma-separated list of values. If comma separated, do not include spaces unless intended for matching.
* callback($callback, $message = '', $params = null) - Define your own custom callback validation function. $callback must pass an is_callable() check. $params can be any value, or an array if multiple parameters must be passed.

##### Validating Arrays and Array Indices

This validation class has been extended to allow for validation of arrays as well as nested indices of a multi-dimensional array.

To validate specific indices of an array, use dot notation, i.e.

    // load the validator
    $validator = new \assegai\modules\validator\Validator($this->request->allPost());

    // ensure $_POST['field']['nested'] exists
    $validator
      ->required('The nested field is required.')
      ->validate('field.nested');

    // ensure we have the first two numeric
    // indices of $_POST['links'][]
    $validator
      ->required('This field is required')
      ->validate('links.0');
    $validator
      ->required('This field is required')
      ->validate('links.1');

##### Available Pre-Validation Filtering

You can apply pre-validation filters to your data (i.e. trim, strip_tags, htmlentities). These filters can also
be custom defined so long as they pass an <code>is_callable()</code> check.

* filter($callback)

Examples:

    // standard php filter for valid user ids.
    $validator
      ->filter('intval')
      ->min(1)
      ->validate('user_id');

    // custom filter
    $validator
      ->filter(function($val) {
        // bogus formatting of the field
        $val = rtrim($val, '/');
        $val .= '_custom_formatted';
      })
      ->validate('field_to_be_formatted');
