<?php

namespace assegai\modules\pdo;

use \assegai\modules;

/**
 * @package assegai.module.pdo
 */
class Pdo extends modules\Module
{
    /** Currently-running connections. */
    protected $connections;

    public static function instanciate()
    {
        return true;
    }

    public static function dependencies()
    {
        return array(
            array(
                'name' => 'module_pdo',
                'class' => 'assegai\\modules\\pdo\\Pdo',
                'mother' => 'module',
            ),
        );
    }

    public function setOptions($options)
    {
        parent::setOptions($options);
        
        $this->connections = array();

        // Opening connections.
        foreach ($options as $conn => $spec) {
            $this->connections[$conn] = new \PDO(
                $spec['dsn'],
                $spec['username'],
                $spec['password'],
                @$spec['options']
            );
        }
    }

    public function __get($name)
    {
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        } else {
            throw new \Exception("No such connection: `$name'");
        }
    }
}
