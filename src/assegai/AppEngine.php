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

namespace assegai {
  /**
   * Applications dispatcher.
   *
   * This is the main class of Assegai and a routing wrapper around
   * Atlatl. The principle is simple; the request is routed to the
   * correct Atlatl appliction, then the output is processed by the
   * global settings.
   */
    class AppEngine
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

        protected $root_path;
        protected $apps;

        protected $main_conf;
        protected $apps_conf;

        protected $current_app;
        protected $prefix;  

        // Dependency setters.
        function setDependencies(Server $server, modules\ModuleContainer $container, Security $security, routing\IRouter $router)
        {
            $this->root_path = dirname(__DIR__);
            $this->server = $server;
            $this->router = $router;

            $this->register40x(function(\Exception $e) {
                return array(
                    'request' => null,
                    'response' => new Response('404 Error - Page not found', 404),
                );
            });
            $this->register50x(function(\Exception $e) {
                return array(
                    'request' => null,
                    'response' => new Response('500 Error - Server error', 404),
                );
            });

            $this->modules = $container;

            $this->security = $security;

            $this->conf_path = $this->getPath('conf.php');
        }

        function setConfiguration($path)
        {
            $this->conf_path = $path;
        }

        /**
         * Parses the global configuration file and that of each
         * application.
         */
        protected function parseconf()
        {
            // Loading the main configuration file first so we can get the paths.
            $this->conf = Config::fromArray(array(
                'prefix' => '',
                'apps_path' => 'apps',
                'models_path' => 'models',
                'helpers_path' => 'helpers',
                'exceptions_path' => 'exceptions',
                'modules_path' => 'lib/modules',
                'apps' => array(),
                'modules' => array(),
            )); // Defaults
            $this->conf->loadFile($this->conf_path);

            if($this->conf->get('prefix')) {
                $this->server->setPrefix($this->prefix);
            }

            // Alright. Now let's load the apps config. We'll merge the apps routes as we go along.
            foreach($this->conf->get('apps', array()) as $appname) {
                $path = Utils::joinPaths($this->conf->get('apps_path'), $appname, 'conf.php');
                
                try {
                    $this->apps_conf[$appname] = Config::fromFile($path, 'app');
                }
                catch(\Exception $e) {
                    continue;
                }
                
                // Little shortcut to help readability
                $app = $this->apps_conf[$appname];
                $this->router->setRoutes($appname, $app->get('route'));

                // Let's merge in the modules, they'll be common to all apps.
                $modules = $this->conf->get('modules', array());
                if($app->get('modules')) {
                    foreach($app->get('modules') as $module) {
                        if(!in_array($module, $modules)) {
                            $modules[] = $module;
                            if($app->get($module)) { // Module-specific options.
                                $this->conf->set($module, $app->get($module));
                            }
                        }
                    }
                }
            }
        }

        /**
         * Returns an absolute path.
         */
        protected function getPath($relpath)
        {
            // Is this a relative path?
            if($relpath[0] == '/'
            || preg_match('/^[a-z]:/i', $relpath)) {
                return $relpath;
            } else {
                return $this->root_path . '/' . $relpath;
            }
        }

        /**
         * Serves requests.
         *
         * @param $request is a Request object to handle. If left out, the request will
         *      be generated from the environment.
         * @param $return_response: whether to process the response or to return it. Default
         * to process (false).
         */
        public function serve(Request $request = null, $return_response = false)
        {
            $this->parseconf();
            $this->request = $request;

            if(!$request) {
                $request = $this->request;
            }

            $response = null;
            
            $autoloader = new Autoloader();
            $autoloader->setConf($this->conf);

            try {
                // We register the dispatcher's autoloader
                spl_autoload_register(array($autoloader, 'autoload'));
                $this->sethandlers();
                $result = $this->doserve($request);
            }
            catch(\assegai\exceptions\HttpRedirect $r) {
                $result = array(
                    'request' => $request,
                    'response' => new Response());
                $result['response']->setHeader('Location', $r->getUrl());
            }
            catch(\assegai\exceptions\HTTPNotFoundError $e) {
                $result = call_user_func($this->error40x, $e);
            }
            catch(\assegai\exceptions\HTTPClientError $e) {
                $result = call_user_func($this->error40x, $e);
            }
            catch(\assegai\exceptions\HTTPServerError $e) {
                $result = call_user_func($this->error50x, $e);
            }
            // Generic HTTP status response.
            catch(\assegai\exceptions\HTTPStatus $s) {
                $result = array(
                    'request' => $request,
                    'response' => new Response($s->getMessage(), $s->getCode()));
            }
            catch(\Exception $e) {
                $result = call_user_func($this->error50x, $e);
            }

            if(!$result['request']) {
                $result['request'] = $request;
            }

            if($return_response) {
                return $result;
            } else {
                return $this->display($result['request'], @$result['response']);
            }
        }

        /**
         * Simulates or forces a HTTP request. This is a temporary hack that
         * permits using Assegai within something else or to run it as a CLI
         * app.
         */
        public function execute($url = '',
        array $get = array(),
        array $post = array(),
        array $session = array(),
        array $cookies = array()) {
            $request = new Request($url, $get, $post, new Security(), $session, $cookies);
            $result = $this->serve($request, true);

            $answer = new \StdClass(); 
            $answer->session = $result['request']->getAllSession();
            $answer->cookies = $result['request']->getAllCookies();
            if(is_object($result['response'])) {
                $answer->result = $result['response']->getBody();
            } else {
                $answer->result = $response;
            }

            return $answer;
        }

        /**
         * Processes the returned object from a handler.
         */
        protected function display(Request $request, $response) {
            $request->saveState();
            if(is_object($response)) {
                $response->compile();
            } else {
                echo $response;
            }
        }

        /**
         * Prepares the error handlers.
         */
        protected function sethandlers() {
            if($this->conf->get('handler40x')) {
                $this->register40x($this->makeErrorHandler($this->conf->get('handler40x')));
            } else {
                $this->register40x(array($this, 'notfoundhandler'));
            }
            if($this->conf->get('handler50x')) {
                $this->register50x($this->makeErrorHandler($this->conf->get('handler50x')));
            } else {
                $this->register50x(array($this, 'errorhandler'));
            }
        }

        /**
         * Generates an error-handling closure.
         */
        protected function makeErrorHandler($handler) {
            $dispatcher = $this;
            $server = $this->server;
            $request = $this->request;
            $conf = $this->conf;
            $apps_conf = $this->apps_conf;
            
            return function($e) use($dispatcher, $handler, $server, $request, $conf, $apps_conf) {
                list($class, $method) = explode('::', $handler);

                // If the controller's name conforms to conventions, then we can get the app name.
                if(strpos($class, '\\') !== false) { // New PSR-0 style.
                    list($app_name, $token, $controller_name) = explode('\\', trim($class, '\\'));
                } else {
                    list($app_name, $token, $controller_name) = explode('_', strtolower($class));
                }

                $server->setAppName($app_name);
                $server->setMainConf($conf);
                $server->setAppConf($apps_conf[$app_name]);
                $server->setAppPath(Utils::joinPaths($conf->get('apps_path'), $app_name));

                if($token == 'controller' || $token == 'controllers') {
                    try {
                        $modules = $dispatcher->loadAppModules($app_name);
                    }
                    catch(\Exception $e) {
                        $modules = new modules\ModuleContainer($server);
                    }
                } else {
                    $modules = new modules\ModuleContainer($server);
                }

                $controller = new $class(
                    $modules,
                    $server,
                    $request,
                    new \assegai\Security()
                );

                $this->modules->preRequest($controller, $request);

                $controller->preRequest();
                $page = $controller->$method($e);
                $controller->postRequest($page);

                return array('request' => $request, 'response' => $page);
            };
        }

        /**
         * Processes a route call, something like `stuff::thing' or just a function name
         * or even a closure.
         */
        protected function process(routing\RouteCall $proto, $request) {
            /* We're accepting different types of handler declarations. It can be
             * anything PHP defines as a 'callable', or in the form class::method. */
            $class = '';
            $method = '';
            $call = $proto->getCall();
            $params = $proto->getParams();

            if(is_string($call) && preg_match('/^.*::.+$/', trim($call))) {
                list($class, $method) = explode('::', $call);
            }
            else if(is_array($call)) {
                $class = $call[0];
                $method = $call[1];
            }
            else if(is_callable($call)) {
                $method = $call;
            }

            // Detecting simple routes that use implicit namespacing.
            if(stripos($class, 'controller') === false) {
                $class = sprintf('%s\\controllers\\%s', strtolower($this->current_app), $class);
            }

            if(!$class) {
                $class = '\\assegai\\Controller';
            } else {
                $class = '\\' . $class;
            }

            $response = null;

            $this->modules->runMethod('preProcess', array(
                'request' => $request,
                'proto' => $proto,
                'response' => $response,
            ));

            if(!$class) { // Just a function call (or a closure?). Less hooks obviously.
                // Mounting system stuff into an object and generating the parameters.
                $params = array_merge(array((object)array(
                    'modules' => $this->modules,
                    'server'  => $this->server,
                    'request' => $request,
                    'sec'     => $this->security)),
                $params);
                $response = call_user_func_array($method, $params);
            }
            else if(class_exists($class)) {
                $obj = new $class($this->modules, $this->server,
                    $request, new Security());

                $this->modules->preRequest($obj, $request);

                if(method_exists($obj, 'preRequest')) {
                    $obj->preRequest();
                }   

                if(method_exists($obj, $method)) {
                    $response = call_user_func_array(array($obj, $method),
                    $params);
                    if(method_exists($obj, 'postRequest'))
                        $response = $obj->postRequest($response);
                } else {
                    throw new \BadMethodCallException("Method '$method' not found in class '$class'.");
                }
            } else {
                throw new exceptions\NoHandlerException("Class '$class' not found.");
            }

            // Cleaning up the response...
            if(gettype($response) == 'string') {
                $response = new Response($response);
            }
            else if($response === null) {
                $response = new Response();
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

            return array('response' => $response, 'request' => $request);
        }

        /**
         * Actually does the job of serving pages.
         */
        protected function doserve(Request $request)
        {
            $route_to_app = "";
            $app = null;
            
            $call = $this->router->getRoute($request);
            
            $this->current_app = $call->getApp();
            $this->server->setAppName($this->current_app);

            $this->server->setMainConf($this->conf);
            $this->server->setAppConf($this->apps_conf[$this->current_app]);
            $this->server->setAppPath(Utils::joinPaths($this->conf->get('apps_path'), $this->current_app));
            if($this->apps_conf[$this->current_app]->get('use_session')) {
                session_start();
                $request->setAllSession($_SESSION);
                $this->request = $request;
            }

            // Let's load the app's modules
            $this->loadAppModules($this->current_app);
            
            return $this->process($call, $request);
        }

        function loadAppModules($app) {
            if($this->conf->get('modules')
            	&& is_array($this->conf->get('modules'))) {
                foreach($this->conf->get('modules') as $module) {
                    $opts = array();
                    
                    if($this->apps_conf[$app]->get($module)) {
                        // We give priority to the app's module configuration.
                        $opts = $this->apps_conf[$app]->get($module);
                    }
                    elseif($this->conf->get($module)) {
                        $opts = $this->conf->get($module);
                    }
                    
                    $this->modules->addModule($module, $opts);
                }
			}
            if($this->apps_conf[$app]->get('modules')
				&& is_array($this->apps_conf[$app]->get('modules'))) {
                // Now the app's modules turn.
                foreach($this->apps_conf[$app]->get('modules', array()) as $module) {
                    if(in_array($module, $this->conf->get('modules'))) {
                        continue;
                    }
                    $opts = NULL;
                    $opts = $this->apps_conf[$app]->get($module, array());
                    $this->modules->addModule($module, $opts);
                }
            }
        }

        function notfoundhandler($e)
        {
            if(isset($_SERVER['APPLICATION_ENV'])
                && $_SERVER['APPLICATION_ENV'] == 'development') {
                $server = $this->server;
                require('notfoundview.phtml');
            } else {
                return array(
                    'request' => null,
                    'response' => new Response('Not found!', 404),
                );
            }
        }

        function errorhandler($e)
        {
            if(isset($_SERVER['APPLICATION_ENV'])
                && $_SERVER['APPLICATION_ENV'] == 'development') {
                $printtrace = function($error) {
                    $trace = $error->getTrace();
                    $formatted_trace = array();
                    for($i = 0; $i < count($trace); $i++) {
                        $line = '';
                        if(true || strpos($trace[$i]['class'], 'assegai\\') === false) {
                            $line = "$i - ";
                            if($trace[$i]['class']) {
                                $line.= "at " . $trace[$i]['class'] . "::";
                            }
                            if($trace[$i]['function']) {
                                $line.= $trace[$i]['function'] . "() ";
                            }
                            $line.= sprintf("in %s on line %s",
                            $trace[$i]['file'],
                            $trace[$i]['line']);
                        }
                        $formatted_trace[] = $line;
                    }

                    return implode(PHP_EOL, $formatted_trace);
                };
                require('errorview.phtml');
            } else {
                return array(
                    'request' => null,
                    'response' => new Response($e->getCode() . " Error!", $e->getCode()),
                );
            }
        }

        /**
         * Sets a new handler for 404 errors.
         * @param callable $handler will be called in the event of a 404
         * error. This callable must accept one Exception parameter.
         */
        public function register40x($handler)
        {
            $this->error40x = $handler;
        }

        /**
         * Sets a new handler for 50x errors.
         * @param callable $handler will be called in the event of a 500
         * error. This callable must accept one Exception parameter.
         */
        public function register50x($handler)
        {
            $this->error50x = $handler;
        }

        /**
         * Wrapper that converts PHP errors to exceptions and passes them
         * to the standard error50x handler.
         */
        public function php_error_handler($errno, $errstr, $errfile, $errline)
        {
            $e = new \Exception($errstr, $errno);
            call_user_func($this->error50x, $e);
        }

        /**
         * Instanciates a new module and adds it to the collection.
         * @param string $module is the module's name.
         * @param array $options is an array of options passed to the
         * module's constructor.
         */
        public function loadModule($module, $options = NULL)
        {
            $this->modules->addModule($module, $options);
        }

        /**
         * Replaces the current modules container by the provided one.
         * @param ModuleContainer $container is a container of modules.
         */
        public function setModules(ModuleContainer $container)
        {
            $this->modules = $container;
        }

        /**
         * Changes the URL prefix to work from.
         * @param string $prefix is the URL prefix to use, for instance "/glue".
         * @return this object (you can make a call chain).
         */
        public function setPrefix($prefix)
        {
            // We ensure that the prefix is properly formatted. It
            // must start with a '/' and end without one.
            if($prefix != "") {
                if($prefix[0] != '/') {
                    $prefix = '/' . $prefix;
                }
                if($prefix[strlen($prefix) - 1] == '/') {
                    $prefix = substr($prefix, 0, strlen($prefix) - 1);
                }
            }

            $this->prefix = $prefix;
            return $this;
        }
    }
}
