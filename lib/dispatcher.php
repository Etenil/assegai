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
    protected $modules_path;
	protected $apps;

	protected $apps_conf;

	protected $current_app;
	protected $prefix;

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
		$conf = array(
			'prefix' => '',
			'apps_path' => 'apps',
            'modules_path' => 'lib/modules',
			'apps' => array(),
			);

		require($this->getPath('conf.php'));
		$this->apps_path = $this->getPath($conf['apps_path']);
        $this->modules_path = $this->getPath($conf['modules_path']);
		$this->apps = $conf['apps'];
		$this->prefix = $conf['prefix'];

		// Alright. Now let's load the apps config.
		$this->routes = array();
		$this->app_routes = array();
		foreach($this->apps as $appname) {
			$path = $this->apps_path . '/' . $appname;
			if(!file_exists($path) || !is_dir($path)) {
				continue;
			}
			$app = array();
			@include($path . '/conf.php');
			$this->apps_conf[$appname] = $app;
			foreach($app['route'] as $route => $callback) {
				$this->app_routes[$route] = $appname;
			}
		}

		krsort($this->app_routes);
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
	 * Autoloader for controllers etc.
	 */
	public function autoload($classname)
	{
		$splitter = strpos($classname, '_');
		if($splitter !== false) {
			$type = substr($classname, 0, $splitter);
			$class = substr($classname, $splitter + 1);

            $filename = "";
            if($type == 'Module') {
                $filename = $this->modules_path . '/' . strtolower($class) . '/' .
					strtolower($class) . '.php';
            } else {
                $paths = array('Controller' => 'controllers',
                               'Model' => 'models',
                               'View' => 'views');
                $filename = $this->apps_path . '/' . $this->current_app . '/'
                    . $paths[$type] . '/' . strtolower($class) . '.php';
            }

            @include($filename);
		}
	}

	/**
	 * Serves requests
	 */
	public function serve()
	{
		$server = new \atlatl\Server($_SERVER);
		$route_to_app = "";
        $app = null;

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

		$this->current_app = $app;

		// We register the dispatcher's autoloader
		spl_autoload_register(array($this, 'autoload'));

		$runner = new \Atlatl\Core($this->prefix, $server);

		// Let's load the app's modules
		$container = new ModuleContainer();
		if(isset($this->apps_conf[$app]['modules'])) {
			foreach($this->apps_conf[$app]['modules'] as $module) {
				$opts = NULL;
				if(isset($this->apps_conf[$app][$module])) {
					$opts = $this->apps_conf[$app][$module];
				}
				$container->addModule('Module_' . $module, $opts);
			}
		}

		$runner->setModules($container);
		$runner->serve($this->apps_conf[$app]['route']);
	}
}

?>