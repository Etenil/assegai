<?php

namespace assegai\injector;

use \assegai\exceptions;

class DependenciesDefinition
{
    const INJECT_CONSTRUCTOR = 1; // Inject through constructor (default)
    const INJECT_EACH_SETTER = 2; // Inject through individual setters
    const INJECT_BIG_SETTER = 3; // Inject through one big setter named "setDependencies"

    protected $name;
    protected $class;
    protected $dependencies;
    protected $type = self::INJECT_CONSTRUCTOR;
    protected $mother = null;

    public static function fromArray(array $a_def)
    {
        // The array must have a name and class. Or else...
        if (!isset($a_def['name'])) {
            throw new exceptions\HttpInternalServerError(
                "Name undefined in dependency definition"
            );
        }
        if (!isset($a_def['class'])) {
            throw new exceptions\HttpInternalServerError(
                "Class name undefined in dependency definition '" . $a_def['name'] . "'"
            );
        }

        $def = new self();
        $def->setName($a_def['name']);
        $def->setClass($a_def['class']);

        if (isset($a_def['dependencies'])) {
            $def->setDependencies($a_def['dependencies']);
        }
        if (isset($a_def['mother'])) {
            $def->setMother($a_def['mother']);
        }
        if (isset($a_def['type'])) {
            $def->setType($a_def['type']);
        }

        return $def;
    }

    // Accessors.
    public function getName()
    {
        return $this->name;
    }

    public function setName($val)
    {
        $this->name = $val;
        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setClass($val)
    {
        $this->class = $val;
        return $this;
    }

    public function getDependencies()
    {
        return $this->dependencies ?: array();
    }

    public function setDependencies($val)
    {
        $this->dependencies = $val;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($val)
    {
        $this->type = $val;
        return $this;
    }

    public function getMother()
    {
        return $this->mother;
    }

    public function setMother($val)
    {
        $this->mother = $val;
        return $this;
    }
}
