<?php

/**
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

namespace assegai
{
    class Framework
    {
        protected $container;

        function __construct()
        {
            $this->container = new injector\Container();

            //// Setting core dependencies.
            // Core.
            $inject_type = injector\DependenciesDefinition::INJECT_BIG_SETTER;

            $this->container->loadConf(array(
                array(
                    'name' => 'engine',
                    'class' => 'assegai\\AppEngine',
                    'dependencies' => array('server', 'mc', 'security', 'router'),
                    'type' => $inject_type,
                ),
                array(
                    'name' => 'router',
                    'class' => 'assegai\\routing\\DefaultRouter',
                ),
                array(
                    'name' => 'server',
                    'class' => 'assegai\\Server',
                    'type' => $inject_type,
                ),
                array(
                    'name' => 'request',
                    'class' => 'assegai\\Request',
                    'dependencies' => array('server', 'security'),
                    'type' => $inject_type,
                ),
                array(
                    'name' => 'mc',
                    'class' => 'assegai\\modules\\ModuleContainer',
                    'dependencies' => array('server'),
                    'type' => $inject_type,
                ),
                array(
                    'name' => 'module',
                    'class' => 'assegai\\modules\\ModuleInjected',
                    'dependencies' => array('server', 'mc'),
                    'type' => $inject_type,
                ),
                array(
                    'name' => 'response',
                    'class' => 'assegai\\Response',
                ),
                array(
                    'name' => 'security',
                    'class' => 'assegai\\Security',
                ),
            ));
        }

        function setConfigPath($conf_path)
        {
            $this->conf_path = $conf_path;
        }

        function serve($request = null)
        {
            $engine = $this->container->give('engine');
            $engine->setConfiguration($this->conf_path);
            $engine->serve($request);
        }

        function run($conf_path = '')
        {
            $request = $this->container->give('request');
            $request->fromGlobals();
            
            $this->setConfigPath($conf_path);
            $this->serve($request);
        }

        /**
         * Triggers the run of a URI from the command line. The
         * GET and POST data in the request will be set to the value
         * of the command line arguments. See Request::fromCli() for
         * more details on command line arguments parsing.
         *
         * @param string $uri the URI to trigger the run for.
         * @param optional string $conf_path where to find the
         *        framework configuration.
         */
        function runuri($uri, $conf_path = '')
        {
            if($conf_path) {
                $this->setConfigPath($conf_path);
            }

            $request = $this->container->give('request');
            $request->fromCli($uri);

            $this->serve($request);
        }
    }
}

