<?php

namespace assegai\modules\pdo;

/**
 * @package assegai.module.pdo
 */
class Pdo extends \assegai\Module
{
    /** Currently-running connections. */
    protected $connections;

    public static function instanciate()
    {
        return true;
    }

    function _init($options)
    {
        $this->connections = array();

        // Opening connections.
        foreach($options as $conn => $spec) {
            $this->connections[$conn] = new \PDO(
                $spec['dsn'],
                $spec['username'],
                $spec['password'],
                $spec['options']
            );
        }
    }

    function __get($name)
    {
        if(isset($this->connections[$name])) {
            return $this->connections[$name];
        } else {
            throw new Exception("No such connection: `$name'");
        }
    }
}
