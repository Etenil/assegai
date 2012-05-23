<?php

class Module_PDO extends \assegai\Module
{
    /** Currently-running connections. */
    protected $connections;

    function __construct($options)
    {
        $this->connections = array();

        // Opening connections.
        foreach($options as $conn => $spec) {
            $this->connections[$conn] = new PDO($spec['dsn'],
                                                $spec['username'],
                                                $spec['password'],
                                                $spec['options']);
        }
    }

    function __get($name) {
        if(isset($this->connections[$name])) {
            return $this->connections[$name];
        } else {
            throw new Exception("No such connection: `$name'");
        }
    }
}
