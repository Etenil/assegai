<?php

/**
 * Dependency injector for Assegai.
 *
 * This file is part of Assegai
 *
 * Copyright (c) 2013 Guillaume Pasquet
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace assegai\injector
{
    /**
     * Dependency injector for assegai.
     */
    class Container {
        protected $definitions;
        protected $mother;

        public function __construct(Container $mother = null)
        {
            $this->definitions = array();
            $this->mother = $mother;
        }

        /**
         * Defines a new instanciation function.
         */
        public function register(DependenciesDefinition $def)
        {
            $this->definitions[$def->getName()] = $def;
        }

        /**
         * Loads up dependency configuration as an array structure.
         */
        public function loadConf(array $conf)
        {
            foreach($conf as $full_def) {
                $def = DependenciesDefinition::fromArray($full_def);

                $this->register($def, $classname, $dependencies, $type);
            }
        }

        public function giveDefinition($def)
        {
            if(!array_key_exists($def, $this->definitions)) {
                if(is_object($this->mother)) {
                    return $this->mother->giveDefinition($def);
                } else {
                    return false;
                }
            }

            return $this->definitions[$def];
        }

        public function give($def)
        {
            if(!array_key_exists($def, $this->definitions)) {
                if(is_object($this->mother)) {
                    return $this->mother->give($def);
                } else {
                    echo "key $def is undefined.";
                    // Attempting to instanciate without parameters.
                    throw new \Exception("Couldn't find definition for $def.");
                }
            }

            $mydef = $this->definitions[$def];
            $motherdef = false;
            
            if(is_object($this->mother)) {
                $motherdef = $this->mother->giveDefinition($mydef->getMother() ?: $mydef->getName());
            }

            $inject_order = array($mydef, $motherdef);
            
            if($motherdef && $motherdef->getType() == DependenciesDefinition::INJECT_CONSTRUCTOR
               && $mydef->getType() != DependenciesDefinition::INJECT_CONSTRUCTOR) { // Always priority
                $inject_order = array($motherdef, $mydef);
            }

            $object = null;
            foreach($inject_order as $def) {
                $object = $this->inject($def, $object);
            }

            return $object;
        }

        protected function inject($def, $object = null) {
            if(!$def) {
                return $object;
            }
            
            $classname = $def->getClass();
            $dependencies = $def->getDependencies();
            $type = $def->getType();

            // Trying to resolve definitions.
            $deps = array(); // Will hold the deps for the constructor.
            foreach($dependencies as $dependency)
            {
                $deps[$dependency] = $this->give($dependency);
            }

            if(!$object) {
                switch($type) {
                case DependenciesDefinition::INJECT_CONSTRUCTOR:
                    $ref = new \ReflectionClass($classname);
                    $object = $ref->newInstanceArgs($deps);
                    break;
                case DependenciesDefinition::INJECT_BIG_SETTER:
                case DependenciesDefinition::INJECT_EACH_SETTER:
                    $object = new $classname();
                    break;
                default:
                    throw new exceptions\HttpInternalServerError("Unknown injection method.");
                }
            }

            switch($type) {
            case DependenciesDefinition::INJECT_CONSTRUCTOR:
                break;
            case DependenciesDefinition::INJECT_BIG_SETTER:
                call_user_func_array(array($object, 'setDependencies'), $deps);
                break;
            case DependenciesDefinition::INJECT_EACH_SETTER:
                foreach($deps as $name => $dep) {
                    // I know that PHP isn't case sensitive. But I like to feel safe.
                    $object->{'set' . ucwords($name)}($dep);
                }
                break;
            default:
                throw new exceptions\HttpInternalServerError("Unknown injection method.");
            }

            if(method_exists($object, 'setInjector')) { // Automatically injecting self reference.
                $object->setInjector($this);
            }

            return $object;
        }
    }
}

