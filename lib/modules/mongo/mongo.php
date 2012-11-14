<?php

class Module_Mongo extends assegai\Module
{
    protected $connections = null;

    public static function instanciate()
    {
        return true;
    }

    function _init($options)
    {
        $this->connections = array();

        foreach($options as $conn => $spec) {
            $m = new Mongo($options['server']);
            $this->connections[$conn] = $m->{$options['database']};
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

