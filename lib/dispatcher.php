<?php

namespace assegai;

/**
 * Request dispatcher.
 *
 * This is the main class of Assegai and a routing wrapper around
 * Atlatl. The principle is simple; the request is routed to the
 * correct Atlatl appliction, then the output is processed by the
 * global settings.
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
class Dispatcher
{
    protected $root_path;
	protected $apps_path;
	protected $apps;

	protected $apps_routes;
	protected $routes;

    function __construct($root)
    {
        $this->root_path = $root;
        $this->parseconf();
    }

    /**
     * Parses the global configuration file and that of each
     * application.
     */
    protected function parseconf()
    {
		// Loading the main configuration file first so we can get the paths.
		$conf = array();
		require('conf.php');
		$this->apps_path = $conf['apps_path'];
		$this->apps = $conf['apps'];

		// Is this a relative path?
		if(!preg_match('/^[A-Z]:/i', $this->apps_path)
		   && $this->apps_path[0] != '/') {
			$this->apps_path = $this->root_path . '/' . $this->apps_path;
		}

		// Alright. Now let's load the apps config.
		$this->routes = array();
		$this->app_routes = array();
		foreach($this->apps as $app) {
			$path = $this->apps_path . '/' . $app;
			if(!file_exists($path) || !is_dir($path)) {
				continue;
			}
			$app = array();
			@include($path . '/bootstrap.php');
			$routes[$app] = $app['route'];
			foreach($app['route'] as $route => $callback) {
				$this->app_routes[$route] = $app;
			}
		}

		krsort($this->app_routes);
    }

	/**
	 * Serves requests
	 */
	public function serve()
	{
		$server = new atlatl\Server($_SERVER);
		$route_to_app = "";

		$method_routes = preg_grep('%^' . $server->getMethod() . ':%',
								   $this->app_routes);
		
		foreach($this->app_routes as $route => $app) {
			if(preg_match('%^'. $route .'%i', $server->getMethod() . ':' . $server->getRoute())) {
				$route_to_app = $app;
				break;
			}
		}

		// Trying generic.
		if(!$route_to_app) {
			foreach($this->app_routes as $route => $app) {
				if(preg_match('%^' . $route . '%i', $server->getRoute())) {
					$route_to_app = $app;
					break;
				}
			}
		}

		if(!$app) {
			throw new Exception('Not found');
		}

		$app = new Atlatl\Core('', $server);
		$app->serve($this->routes[$app]);
	}
}

?>