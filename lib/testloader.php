<?php

function test_autoload($classname)
{
    $splitter = strpos($classname, '_');
    if($splitter !== false) {
        $type = substr($classname, 0, $splitter);
        $class = substr($classname, $splitter + 1);

        $filename = "";
        if($type == 'Module') {
            $filename = ROOT_PATH . 'lib/modules/' . strtolower($class) . '/' .
                strtolower($class) . '.php';
        } else {
            $paths = array('Controller' => 'controllers',
                           'Model' => 'models',
                           'View' => 'views');
            $filename = APP_PATH . '/' . $paths[$type] . '/' . strtolower($class) . '.php';
        }

        @include($filename);
    }
}

spl_autoload_register('test_autoload');

require('loader.php');
require('testcontroller.php');