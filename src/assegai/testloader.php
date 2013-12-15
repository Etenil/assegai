<?php

function test_autoload($classname)
{
    $first_split = strpos($classname, '_');
        if($first_split) {
            $token = substr($classname, 0, $first_split);

            $filename = "";

            if($token == 'Module') {
                $class = substr($classname, strlen($token) + 1);
                $filename = ROOT_PATH . 'lib/modules/' . strtolower($class) . '/' .
					strtolower($class) . '.php';
            }
            else if(substr_count($classname, '_') >= 2) {
                $app_splitter = strpos($classname, '_');
                $type_splitter = strpos($classname, '_', $app_splitter + 1);

                $app = substr($classname, 0, $app_splitter);
                $type = substr($classname, $app_splitter + 1,
                               $type_splitter - $app_splitter - 1);
                $class = substr($classname, $type_splitter + 1);

                $paths = array('Controller' => 'controllers',
                               'Model' => 'models',
                               'View' => 'views');
                $filename = APPS_PATH . strtolower($app) . '/'
                    . $paths[$type] . '/' . strtolower($class) . '.php';
            }

            include($filename);
		}
}

spl_autoload_register('test_autoload');

require('loader.php');
require('testcontroller.php');