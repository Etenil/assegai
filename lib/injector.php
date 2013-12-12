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
            if(!array_key_exists($def, $this->definitions)) {
                echo "key $def is undefined.";
                // Attempting to instanciate without parameters.
                throw new \Exception("Couldn't find definition for $def.");
            }

            $classname = $this->definitions[$def]['class'];
            $dependencies = $this->definitions[$def]['deps'];

            // Trying to resolve definitions.
            $deps = array(); // Will hold the deps for the constructor.
            foreach($dependencies as $dependency)
            {
                $deps[] = $this->give($dependency);
            }

            $ref = new \ReflectionClass($classname);
            return $ref->newInstanceArgs($deps);
        }
    }
}

