<?php

namespace assegai
{
    class Autoloader
    {
        protected $conf;
        
        function setConf(Config $conf)
        {
            $this->conf = $conf;
        }
        
        function psr0path($classname, $base = '')
        {
            $file = $filename = str_replace('_', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $classname)) . '.php';
            if($base)
            {
                $file = $base . DIRECTORY_SEPARATOR . $filename;
                if(!file_exists($file))
                {
                    $file = $base . DIRECTORY_SEPARATOR . strtolower($filename);
                }
            }
            
            return $file;
        }
        
        /**
         * Autoloader for controllers etc.
         */
        public function autoload($classname)
        {            
            if($classname[0] == '\\')
            {
                $classname = substr($classname, 1);
            }
            
            $token = substr($classname, 0, strpos($classname, '\\'));

            if($token == 'modules') { // Global modules
                $filename = $this->psr0path(
                    str_replace('modules\\', '', $classname),
                    $this->conf->get('custom_modules_path')
                );
            }
            else if($token == 'models') {
                $filename = $this->psr0path(
                    str_replace('models\\', '', $classname),
                    $this->conf->get('models_path')
                );
            }
            else if($token == 'helpers') {
                $filename = $this->psr0path(
                    str_replace('helpers\\', '', $classname),
                    $this->conf->get('helpers_path')
                );
            }
            else if($token == 'exceptions') {
                $filename = $this->psr0path(
                    str_replace('exceptions\\', '', $classname),
                    $this->conf->get('exceptions_path')
                );
            }
            else {
                $filename = $this->psr0path(
                    $classname,
                    $this->conf->get('apps_path')
                );
            }

            if($filename && file_exists($filename)) {
                include($filename);
            }
        }
    }
}
