<?php

/**
 * Request dispatcher.
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

namespace assegai\modules;

use \assegai\injector;
use \assegai\eventsystem\events;

class ModuleContainer
{
    /** Contains instanciated modules that extend Module.*/
    protected $modules;
    protected $server;
    protected $injector;

    /**
     * Class constructor. Nothing to say here.
     */
    public function __construct()
    {
        $this->modules = array();
    }

    public function dependenciesLoaded()
    {
        $this->modules = array();
    }
    
    function setInjector(\assegai\injector\Container $container)
    {
        $this->injector = $container;
    }
    
    public function setServer(\assegai\Server $server)
    {
        $this->server = $server;
    }

	/**
	 * A getter to be able to use the modules directly.
     * @param string $name is the requested property name.
	 */
	public function __get($name) {
		if(isset($this->modules[$name])) {
			return $this->modules[$name];
		} else {
			return false;
		}
	}

	/**
	 * Adds a module to the list.
	 * @param string $module is the module's name to instanciate.
     * @param array $options is an array of options to be passed to
	 * the module's constructor. Default is none.
	 */
    public function addModule($module, array $options = NULL) {
        // We need to try the user's modules first, so they can override built-in ones.
        $full_module = '\\modules\\' . strtolower($module) . '\\' . ucwords($module);
        if(!class_exists($full_module)) {
            $full_module = 'assegai\\modules\\' . $module . '\\' . ucwords($module);
            if(!class_exists($full_module)) { // Very last chance.
                $full_module = 'Module_' . $module;
            }
        }

        if($full_module::instanciate())
        {
            $module_instance = null;
            $module_injector = new injector\Container($this->injector);
            $deps = array();
            
            if(method_exists($full_module, 'dependencies')) {
                $deps = $full_module::dependencies();
            } else { // We'll make up a dependency for you.
                $deps = array(
                    array(
                        'name' => 'module_' . $module,
                        'class' => $full_module,
                        'mother' => 'module',
                    ),
                );
            }

            $module_injector->loadConf($deps);
            $module_instance = $module_injector->give('module_' . $module);
            $module_instance->setOptions($options);

            $this->add_to_list($module, $module_instance);
        }
    }

	/**
	 * Adds an instanciated module to the list.
	 * @param string $modulename is the name of the module.
	 * @param Module $module is an instance of a module.
	 */
	public function add_to_list($modulename, Module $module)
	{
		$this->modules[$modulename] = $module;
	}

	/**
	 * Ensures a module is loaded.
	 * @param string $modname is the module's name.
	 * @return boolean TRUE if the module is here, FALSE otherwise.
	 */
	public function isLoaded($modname) {
		return isset($this->modules[$modname]);
	}

	/**
	 * Runs the same method across all modules.
	 * @param string $method_name is the method to be used on all modules.
	 * @param array $params is an array of parameters to pass to all methods.
	 */
	public function runMethod($method_name, array $params = NULL)
	{
        return $this->batchRun(false, $method_name, $params);
	}

    /**
     * Batch runs a method on all modules.
     * @param bool $is_hook specifies that this is a hook call.
	 * @param string $method_name is the method to be used on all modules.
	 * @param array $params is an array of parameters to pass to all methods.
     */
	public function batchRun($is_hook, $method_name, array $params = NULL)
    {
        // Prevents annoying notices.
		if($params == NULL) {
			$params = array();
		}

        // We collect the results into an array.
        $results = array();
        if(!$this->modules) $this->modules = array();
        foreach($this->modules as $name => $module) {
            if(method_exists($module, $method_name)) {
                $result = call_user_func_array(array($module, $method_name), $params);
                if($is_hook) { // Hooks are pre-emptive if they return something.
                    if($result) {
                        return $result;
                    }
                } else { // Collecting
                    $results[$name] = $result;
                }
            }
		}

        return $is_hook? false : $results;
    }

	/** Mapped module function call.
     * Method called when the module gets initialised. Put custom code
     * here instead of __construct unless you're sure of what you do.
	public function init()
	{ $this->batchRun(true, '_init'); }

	/** Mapped module function call.
     * Pre-routing hook. This gets called prior to the routing
     * callback.
     * @param string $path is the application path.
     * @param string $route is the route that is being queried.
     * @param Request $request is the request object that will be
     * processed.
     */
	public function preRouting($path, $route, events\IEvent $request)
	{ return $this->batchRun(true, 'preRouting', func_get_args()); }

	/** Mapped module function call.
     * Post-routing hook. This gets called after the routing
     * callback.
     * @param string $path is the application path.
     * @param string $route is the route that is being queried.
     * @param Request $request is the request object that will be
     * processed.
     * @param Response $response is the HTTP response produced by the
     * controller.
     */
	public function postRouting($path, $route, events\IEvent $request, \assegai\Response $response)
	{ return $this->batchRun(true, 'postRouting', func_get_args()); }

	/** Mapped module function call.
     * Pre-request handling hook. This gets called just after the
     * controller has been instanciated and before the request is
     * handled by it.
     * @param Controller $controller is the instanciated controller.
     * @param Request $request is the request object that will be
     * processed.
     */
	public function preRequest(\assegai\Controller $controller, events\IEvent $request)
	{ return $this->batchRun(true, 'preRequest', func_get_args()); }

	/** Mapped module function call.
     * Post-request hook. This gets called after the request has been
     * handled by the controller.
     * @param string $path is the application path.
     * @param string $route is the route that is being queried.
     * @param Request $request is the request object that will be
     * processed.
     * @param Response $response is the HTTP response produced by the
     * controller.
     */
	public function postRequest(\assegai\Controller $controller, events\IEvent $request, \assegai\Response $response)
	{ return $this->batchRun(true, 'postRequest', func_get_args()); }

	/** Mapped module function call.
     * Pre-view hook. Gets called just before processing the
     * view.
     * @param string $path is the requested view's path.
     * @param Request $request is the HTTP Request object currently
     * being handled.
     */
	public function preView(events\IEvent $request, $path, $vars)
	{ return $this->batchRun(true, 'preView', func_get_args()); }

	/** Mapped module function call.
     * Post-view hook. Gets called just after having processed the
     * view.
     * @param string $path is the requested view's path.
     * @param array $vars is the HTTP Request object currently
     * being handled.
     * @param string $result response the HTTP Response produced by the
     * view.
     */
	public function postView(events\IEvent $request, $path, $vars, $result)
	{ return $this->batchRun(true, 'postView', func_get_args()); }

   	/** Mapped module function call.
     * Pre-model hook. Gets called just before loading a model.
     * @param string $model_name is the requested model's name.
     */
	public function preModel($model_name)
	{ return $this->batchRun(true, 'preModel', func_get_args()); }

	/** Mapped module function call.
     * Post-model hook. Gets called just after having loaded the
     * model.
     * @param string $model_name is the requested model's name.
     */
	public function postModel($model_name)
	{ return $this->batchRun(true, 'postModel', func_get_args()); }
}
