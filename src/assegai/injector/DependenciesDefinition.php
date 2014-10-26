<?php

namespace assegai\injector;
use \assegai\exceptions;

class DependenciesDefinition
{
    protected $name;
    protected $class;
    protected $dependencies;
    protected $mother = null;

    static function fromArray(array $a_def)
    {
        // The array must have a name and class. Or else...
        if(!isset($a_def['name'])) {
            throw new exceptions\HttpInternalServerError("Name undefined in dependency definition");
        }
        if(!isset($a_def['class'])) {
            throw new exceptions\HttpInternalServerError("Class name undefined in dependency definition '" . $a_def['name'] . "'");
        }

        $def = new self();
        $def->setName($a_def['name']);
        $def->setClass($a_def['class']);
        
        if(isset($a_def['dependencies'])) {
            $def->setDependencies($a_def['dependencies']);
        }
        if(isset($a_def['mother'])) {
            $def->setMother($a_def['mother']);
        }

        return $def;
    }

    // Accessors.
    function getName()
    {
        return $this->name;
    }

    function setName($val)
    {
        $this->name = $val;
        return $this;
    }

    function getClass()
    {
        return $this->class;
    }

    function setClass($val)
    {
        $this->class = $val;
        return $this;
    }

    function getDependencies()
    {
        return $this->dependencies ?: array();
    }

    function setDependencies($val)
    {
        $this->dependencies = $val;
        return $this;
    }

    function getMother()
    {
        return $this->mother;
    }

    function setMother($val)
    {
        $this->mother = $val;
        return $this;
    }
}
