<?php

namespace assegai;

/**
 * Basic module implementation.
 *
 * This file is part of Assegai
 *
 * Assegai is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * Assegai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Assegai.  If not, see <http://www.gnu.org/licenses/>.
 */
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

