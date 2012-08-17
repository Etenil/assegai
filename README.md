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

    class Controller_Test extends assegai\Controller
    {
        function hello()
        {
            return "Hello, World!";
        }
    }

We still need to indicate to the framework that this controller needs to be called when visiting the website. This is done by adding the following contents to the application's *conf.php* file.

    $conf['route'] = array(
         '/' => 'Controller_Hello::hello',
         );

Now you can visit your web server and should see the *Hello, World* message printed.
