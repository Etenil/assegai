<?php

namespace assegai;

/**
 * Dependency injector for assegai.
 */
class Injector {
    protected static $registry;

    /**
     * Defines a new instanciation function.
     */
    public static function register($type, \Closure $instanciator)
    {
        self::$registry[$type] = $instanciator;
    }

    public static function give($type)
    {
        $args = func_get_args();
        array_shift($args);

        if(@array_key_exists($type, self::$registry)) {
            // Calling the instanciator.
            return call_user_func_array(self::$registry[$type], $args);
        } else {
            // Instanciating in the good old way.
            $r = new \ReflectionClass($type);
            return $r->newInstanceArgs($args);
        }
    }
}

// Loading the dependencies now.
require('dependencies.php');
