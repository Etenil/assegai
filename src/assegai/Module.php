<?php

/**
 * Basic module implementation.
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

class Module
{
    protected $server;
    protected $options;

	/**
	 * Default module constructor. Loads options into properties.
     * @param array $options is an associative array whose keys will
     * be mapped to properties for speed populating of the object.
	 */
	function __construct(Server $server, ModuleContainer $modules, $options = NULL)
	{
        $this->server = $server;
        $this->options = $options;
        $this->modules = $modules;

        $this->_init($options);
	}

    /**
     * Method called when the module gets initialised. Put custom code
     * here instead of __construct unless you're sure of what you do.
     */
	public function _init($options)
    {
		if(is_array($options)) {
			foreach($options as $opt_name => $opt_val) {
				if(property_exists($this, $opt_name)) {
					$this->$opt_name = $opt_val;
				}
			}
		}
    }

    /**
     * Whether to instanciate and attach the module upon loading.
     * @return bool true if instanciation is needed, or false.
     */
    public static function instanciate()
    {
        return false;
    }

    public function model($name) {
        return new $name($this->modules);
    }

    /**
     * Just a convenient wrapper to retrieve an option.
     * @param string $option is the option's name to retrieve.
     * @param mixed $default default value returned if option doesn't
     * exist. Default is false.
     * @return the value or default value.
     */
    protected function getOption($option, $default = false)
    {
        return isset($this->options[$option])? $this->options[$option] : $default;
    }

    /**
     * Pre-routing hook. This gets called prior to the routing
     * callback.
     * @param string $path is the application path.
     * @param string $route is the route that is being queried.
     * @param Request $request is the request object that will be
     * processed.
     */
	public function preRouting($path, $route, Request $request) {}

    /**
     * Post-routing hook. This gets called after the routing
     * callback.
     * @param string $path is the application path.
     * @param string $route is the route that is being queried.
     * @param Request $request is the request object that will be
     * processed.
     * @param Response $response is the HTTP response produced by the
     * controller.
     */
	public function postRouting($path, $route, Request $request, Response $response) {}

    /**
     * Pre-view hook. Gets called just before processing the
     * view.
     * @param string $path is the requested view's path.
     * @param Request $request is the HTTP Request object currently
     * being handled.
     */
	public function preView(Request $request, $path, $vars) {}

    /**
     * Post-view hook. Gets called just after having processed the
     * view.
     * @param string $path is the requested view's path.
     * @param Request $request is the HTTP Request object currently
     * being handled.
     * @param Response response is the HTTP Response produced by the
     * view.
     */
    public function postView(Request $request, $path, $vars, $result) {}
}

