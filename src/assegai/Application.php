<?php

/**
 * This file is part of Assegai
 *
 * Copyright (c) 2013 - 2014 Guillaume Pasquet
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

namespace assegai;

use assegai\injector;
use assegai\exceptions;

/**
 * Applications dispatcher.
 *
 * This is the main class of Assegai and a routing wrapper around
 * Atlatl. The principle is simple; the request is routed to the
 * correct Atlatl appliction, then the output is processed by the
 * global settings.
 */
class Application extends injector\Injectable
{
    /** Server object. */
    protected $server;
    /** Request router. */
    protected $router;
    /** Request being handled. */
    protected $request;
    /** Container of modules. */
    protected $modules;

    /** Callable variable that handles client errors. */
    protected $error40x;
    /** Callable variable that handles server errors. */
    protected $error50x;

    protected $path;

    protected $framework_conf;
    protected $conf;

    protected $current_app;
    protected $prefix;
    
    /*! the current application's name. */
    protected $name;

    // Dependency setters.
    public function setServer(Server $server)
    {
        $this->server = $server;
    }
    
    public function setMc(modules\ModuleContainer $modules)
    {
        $this->modules = $modules;
    }
    
    public function setSecurity(Security $security)
    {
        $this->security = $security;
    }
    
    public function setRouter(routing\IRouter $router)
    {
        $this->router = $router;
    }
    
    // Various setters.
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setPath($path)
    {
        $this->path = $path;
        $this->parseconf();
    }
    
    public function _init()
    {
        $this->root_path = dirname(__DIR__);
        $this->conf_path = $this->getPath('conf.php');
        set_error_handler(array($this, 'phpErrorHandler'), E_ALL);
    }
    
    public function setFrameworkConf(Config $conf)
    {
        $this->framework_conf = $conf;
    }

    function setConfig(Config $conf)
    {
        $this->conf = $conf;
    }

    /**
     * Parses the app's configuration file.
     */
    protected function parseconf()
    {
        $this->conf->loadFile(Utils::joinPaths($this->path, 'conf.php'), 'app');
        
        // Little shortcut to help readability
        $this->router->setRoutes($this->conf->get('route', array()));
    }

    /**
     * Serves requests.
     *
     * @param $request is a Request object to handle. If left out, the request will
     *      be generated from the environment.
     * @return bool TRUE if the request was served, FALSE otherwise.
     */
    public function serve(Request $request)
    {
        $response = null;

        $call = $this->router->getRoute($request);
        
        if(!$call) {
            throw new exceptions\HttpNotFoundError();
        }
        
        return $this->display($this->process($call, $request));
    }

    /**
     * Processes the returned object from a handler.
     */
    protected function display($response) {
        if(is_object($response)) {
            $response->compile();
        } else {
            echo $response;
        }
    }
    
    protected function isValidCall($call)
    {
        return is_string($call) && preg_match('/^.*::.+$/', trim($call));
    }
    
    protected function explodeCall($call)
    {
        return explode('::', trim($call));
    }

    /**
     * Processes a route call, something like `stuff::thing' or just a function name
     * or even a closure.
     */
    protected function process(routing\RouteCall $proto, $request) {
        $class = '';
        $method = '';
        $call = $proto->getCall();
        $params = $proto->getParams();
        
        $this->server->setAppName($this->name);
        $this->server->setConf($this->conf);
        $this->server->setAppPath($this->path);
        if($this->conf->get('use_session')) {
            session_start();
            $request->setAllSession($_SESSION);
            $this->request = $request;
        }

        if($this->isValidCall($call)) {
            list($class, $method) = $this->explodeCall($call);
        }
        else if(is_array($call) && $this->isValidCall($call[0])) {
            list($class, $method) = $this->explodeCall($call[0]);
            $params = array_slice($call, 1);
        }
        else {
            throw new exceptions\NoHandlerException();
        }

        if(!$class) {
            $class = '\\assegai\\Controller';
        }
        
        $class = '\\' . $class;

        // Detecting simple routes that use implicit namespacing.
        if(stripos($class, 'controller') === false) {
            $class = sprintf('%s\\controllers\\%s', strtolower($this->name), $class);
        }

        // Cleaning for messy namespace separators.
        $class = preg_replace('/\\\\{2,}/', '\\', trim($class, '\\'));

        $response = null;
        $this->modules->runMethod('preProcess', array(
            'request' => $request,
            'proto' => $proto,
            'response' => $response,
        ));
        
        if(!class_exists($class)) {
            throw new exceptions\NoHandlerException("Class '$class' not found.");
        }

        $obj = null;
        if(is_object($class)) {
            $obj = $class;
        }
        else {
            $obj = new $class(
                $this->modules,
                $this->server,
                $request, new Security()
            );
        }

        $this->modules->preRequest($obj, $request);

        if(method_exists($obj, 'preRequest')) {
            $obj->preRequest();
        }   

        if(method_exists($obj, $method)) {
            $response = call_user_func_array(array($obj, $method), $params);
            if(method_exists($obj, 'postRequest'))
                $response = $obj->postRequest($response);
        } else {
            throw new \BadMethodCallException("Method '$method' not found in class '$class'.");
        }
        // Cleaning up the response...
        if(gettype($response) == 'string') {
            $resp = $this->container->give('response')->setBody($response);
            $response = $resp;
        }
        else if($response === null) {
            $response = $this->container->give('response'); // A blank response.
        }
        else if(gettype($response) != 'object'
            || (gettype($response) == 'object'
            && (get_class($response) != 'assegai\Response'
            && !is_subclass_of($response, 'assegai\Response')))) {
            throw new exceptions\IllegalResponseException('Unknown response.');
        }

        $this->modules->runMethod('postProcess', array(
            'request' => $request,
            'proto' => $proto,
            'response' => $response
        ));

        $request->saveState();
        return $response;
    }
}
