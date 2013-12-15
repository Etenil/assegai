<?php

namespace assegai
{
    class Framework
    {
        protected $injector;

        function __construct()
        {
            $this->injector = new Injector();

            //// Setting core dependencies.
            // Core.
            $this->injector->register('engine', 'assegai\\AppEngine', array('server', 'mc', 'security'));
            $this->injector->register('server', 'assegai\\Server');
            // Request
            $this->injector->register('request', 'assegai\\Request', array('server', 'security'));
            $this->injector->register('mc', 'assegai\\ModuleContainer', array('server'));
            $this->injector->register('response', 'assegai\\Response');
            $this->injector->register('security', 'assegai\\Security');
        }

        function run($conf_path = '')
        {
            $engine = $this->injector->give('engine');
            $engine->setConfiguration($conf_path);
            $request = $this->injector->give('request');
            $request->fromGlobals();
            $engine->serve($request);
        }
    }
}

