<?php

namespace etenil\assegai\modules\mongo;

use \etenil\assegai\modules;

class Mongo extends modules\Module
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
            $m = new \Mongo($spec['server']);
            $this->connections[$conn] = $m->{$spec['db']};
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
