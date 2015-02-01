<?php

namespace etenil\modules\PdoModule;

use \etenil\assegai\modules;

/**
 * @package assegai.module.pdo
 */
class PdoModule extends modules\Module
{
    /** Currently-running connections. */
    protected $connections;

    public static function instanciate()
    {
        return true;
    }

    function setOptions($options)
    {
        parent::setOptions($options);
        
        $this->connections = array();

        // Opening connections.
        foreach($options as $conn => $spec) {
            $this->connections[$conn] = new \PDO(
                $spec['dsn'],
                @$spec['username'], // Optional stuff
                @$spec['password'],
                @$spec['options']
            );
        }
    }

    function __get($name)
    {
        if(isset($this->connections[$name])) {
            return $this->connections[$name];
        } else {
            throw new \Exception("No such connection: `$name'");
        }
    }
}
