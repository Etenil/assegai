<?php

namespace assegai
{

    /**
     * Dependency injector for assegai.
     */
    class Injector {
        protected $definitions;

        public function __construct()
        {
            $this->definitions = array();
        }

        /**
         * Defines a new instanciation function.
         */
        public function register($def, $classname, array $dependencies = array())
        {
            $this->definitions[$def] = array(
                'class' => $classname,
                'deps' => $dependencies,
            );
        }

        public function give($def)
        {
            // TODO: Test this.
            //echo '<pre>';
            //var_dump($this->definitions);

            if(!array_key_exists($def, $this->definitions)) {
                // Attempting to instanciate without parameters.
                throw new Exception("Couldn't find definition for $def.");
            }

            $classname = $this->definitions[$def];
            $dependencies = $this->definitions[$deps];

            // Trying to resolve definitions.
            $deps = array(); // Will hold the deps for the constructor.
            foreach($dependencies as $dependency)
            {
                $deps = $this->give($dependency);
            }

            return call_user_func_array(array($classname, '__construct'), $deps);
        }
    }
}

