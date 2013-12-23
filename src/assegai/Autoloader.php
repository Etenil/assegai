<?php

namespace assegai
{
    class Autoloader
    {
        protected $apps_path = '.';
        
        function setAppsPath($apps_path)
        {
            $this->apps_path = $apps_path;
        }
        
        /**
         * Autoloader for controllers etc.
         */
        public function autoload($classname)
        {
            $first_split = strpos($classname, '_');
            $filename = "";
            
            if($first_split)
            {
                $token = substr($classname, 0, $first_split);

                if($token == 'Module')
                {
                    $class = substr($classname, strlen($token) + 1);

                    // Trying user modules.
                    $filename = '';
                    if($this->custom_modules_path)
                    {
                        $filename = $this->custom_modules_path . '/' . strtolower($class) . '/' .
                            strtolower($class) . '.php';
                    }

                    // Falling back on default module path.
                    if(!file_exists($filename))
                    {
                        $filename = $this->modules_path . '/' . strtolower($class) . '/' .
                            strtolower($class) . '.php';
                    }
                }
                else if($token == 'Model')
                {
                    $class = substr($classname, strlen($token) + 1);
                    $class = str_replace('_', '/', $class);

                    $filename = $this->models_path . '/' . strtolower($class) . '.php';
                }
                else if($token == 'Helper')
                {
                    $class = substr($classname, strlen($token) + 1);
                    $class = str_replace('_', '/', $class);

                    $filename = $this->helpers_path . '/' . strtolower($class) . '.php';
                }
                else if($token == 'Exception')
                {
                    $class = substr($classname, strlen($token) + 1);
                    $class = str_replace('_', '/', $class);

                    $filename = $this->exceptions_path . '/' . strtolower($class) . '.php';
                }
                else if(substr_count($classname, '_') >= 2)
                {
                    $app_splitter = strpos($classname, '_');
                    $type_splitter = strpos($classname, '_', $app_splitter + 1);

                    $app = substr($classname, 0, $app_splitter);
                    $type = substr($classname, $app_splitter + 1,
                    $type_splitter - $app_splitter - 1);
                    $class = substr($classname, $type_splitter + 1);

                    $paths = array('Controller' => 'controllers',
                    'Exception' => 'exceptions',
                    'Model' => 'models',
                    'View' => 'views');
                    $filename = $this->apps_path . '/' . strtolower($app) . '/'
                        . $paths[$type] . '/' . str_replace('_', '/', strtolower($class)) . '.php';
                }
            }
            else
            {
                // PSR-0 autoloader (before was just backwards-compat...)
                $psr0path = function($classname, $base = '')
                {
                    $file = str_replace('_', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $classname)) . '.php';
                    if($base)
                    {
                        $file = $base . DIRECTORY_SEPARATOR . $file;
                        if(!file_exists($file))
                        {
                            $file = $base . DIRECTORY_SEPARATOR . strtolower($file);
                        }
                    }
                    
                    return $file;
                };
                
                if($classname[0] == '\\')
                {
                    $classname = substr($classname, 1);
                }
                
                $token = substr($classname, 0, strpos('\\', $classname));
                
                if($token == 'modules') // Global modules
                {
                    $filename = $psr0path(str_replace('modules\\', '', $classname), $this->custom_modules_path);
                }
                else if($token == 'models')
                {
                    $filename = $psr0path(str_replace('models\\', '', $classname), $this->models_path);
                }
                else if($token == 'helpers')
                {
                    $filename = $psr0path(str_replace('helpers\\', '', $classname), $this->helpers_path);
                }
                else if($token == 'exceptions')
                {
                    $filename = $psr0path(str_replace('exceptions\\', '', $classname), $this->exceptions_path);
                }
                else
                {
                    $filename = $psr0path($classname, $this->apps_path);
                }
            }

            if($filename && file_exists($filename))
            {
                include($filename);
            }
        }
    }
}
