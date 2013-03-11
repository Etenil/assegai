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
    protected $models_path;
    protected $exceptions_path;
    protected $modules_path;
    protected $custom_modules_path;
	protected $apps;

    protected $main_conf;
	protected $apps_conf;

	protected $current_app;
	protected $prefix;

	protected $apps_routes;
	protected $routes;

    function __construct($conf = false)
    {
        $this->root_path = dirname(__DIR__);
        $this->conf_path = ($conf? $conf : $this->getPath('conf.php'));
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
            'models_path' => 'models',
            'exceptions_path' => 'exceptions',
            'modules_path' => 'lib/modules',
			'apps' => array(),
            'modules' => array(),
			);

        if(file_exists($this->conf_path)) {
            require($this->conf_path);
        }

		$this->apps_path = $this->getPath($conf['apps_path']);
        $this->models_path = $this->getPath($conf['models_path']);
        $this->exceptions_path = $this->getPath($conf['exceptions_path']);
        $this->modules_path = $this->getPath($conf['modules_path']);
        $this->custom_modules_path = isset($conf['user_modules']) ? $conf['user_modules'] : false;
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

            // Let's merge in the modules, for backwards compatibility.
            if(isset($app['modules'])) {
                foreach($app['modules'] as $module) {
                    if(!in_array($module, $conf['modules'])) {
                        $conf['modules'][] = $module;
                        if(isset($app[$module])) {
                            $conf[$module] = $app[$module];
                        }
                    }
                }
            }

			$this->apps_conf[$appname] = Config::fromArray($app);
			foreach($app['route'] as $route => $callback) {
				$this->app_routes[$route] = $appname;
			}
		}

        $this->main_conf = Config::fromArray($conf);

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
        $first_split = strpos($classname, '_');
        if($first_split) {
            $token = substr($classname, 0, $first_split);

            $filename = "";

            if($token == 'Module') {
                $class = substr($classname, strlen($token) + 1);

                // Trying user modules.
                $filename = '';
                if($this->custom_modules_path) {
                    $filename = $this->custom_modules_path . '/' . strtolower($class) . '/' .
                        strtolower($class) . '.php';
                }

                // Falling back on default module path.
                if(!file_exists($filename)) {
                    $filename = $this->modules_path . '/' . strtolower($class) . '/' .
                        strtolower($class) . '.php';
                }
            }
            else if($token == 'Model') {
                $class = substr($classname, strlen($token) + 1);
                $class = str_replace('_', '/', $class);

                $filename = $this->models_path . '/' . strtolower($class) . '.php';
            }
            else if($token == 'Exception') {
                $class = substr($classname, strlen($token) + 1);
                $class = str_replace('_', '/', $class);

                $filename = $this->exceptions_path . '/' . strtolower($class) . '.php';
            }
            else if(substr_count($classname, '_') >= 2) {
                $app_splitter = strpos($classname, '_');
                $type_splitter = strpos($classname, '_', $app_splitter + 1);

                $app = substr($classname, 0, $app_splitter);
                $type = substr($classname, $app_splitter + 1,
                               $type_splitter - $app_splitter - 1);
                $class = substr($classname, $type_splitter + 1);

                $paths = array('Controller' => 'controllers',
                               'Exception' => 'exceptions',
                               'Model' => 'models',
                               'View' => 'views');
                $filename = $this->apps_path . '/' . strtolower($app) . '/'
                    . $paths[$type] . '/' . strtolower($class) . '.php';
            }

            if($filename) {
                include($filename);
            }
		}
	}

	/**
	 * Serves requests
	 */
	public function serve()
	{
        try {
            $this->doserve();
        }
        catch(\Exception $e) {
            $this->errorhandler($e);
        }
    }

    /**
     * Actually does the job of serving pages.
     */
    protected function doserve()
    {
        // We register the dispatcher's autoloader
		spl_autoload_register(array($this, 'autoload'));

		$server = new Server($_SERVER, $this->prefix);
        $runner = new \atlatl\Core($this->prefix, $server);
		$route_to_app = "";
        $app = null;

        /* Dealing with the error handlers.*/
        if($this->main_conf->get('handler40x')) {
            $handler = $this->main_conf->get('handler40x');
            $runner->register40x(function($e) use($handler, $server) {
                    list($class, $method) = explode('::', $handler);
                    $controller = new $class(new ModuleContainer($server),
                                             $server, new Request($_GET, $_POST,
                                                                  new \atlatl\Security()),
                                             new \atlatl\Security());
                    $page = $controller->$method($e);
                    if(is_string($page)) {
                        return new Response($page);
                    } else {
                        return $page;
                    }
                });
        } else {
            // Default
            $runner->register40x(array($this, 'errorhandler'));
        }
        if($this->main_conf->get('handler50x')) {
            $handler = $this->main_conf->get('handler50x');
            $runner->register50x(function($e) use($handler, $server) {
                    list($class, $method) = explode('::', $handler);
                    $controller = new $class(new ModuleContainer($server),
                                             $server, new Request($_GET, $_POST,
                                                                  new \atlatl\Security()),
                                             new \atlatl\Security());
                    $page = $controller->$method($e);
                    if(is_string($page)) {
                        return new Response($page);
                    } else {
                        return $page;
                    }
                });
        } else {
            $runner->register50x(array($this, 'errorhandler'));
        }

		$method_routes = preg_grep('%^' . $server->getMethod() . ':%',
								   $this->app_routes);

		foreach($method_routes as $route => $app) {
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

		if(!$route_to_app) {
			throw new \Exception('Not found');
		}

		$this->current_app = $route_to_app;

        $server->setMainConf($this->main_conf);
        $server->setAppConf($this->apps_conf[$this->current_app]);
        $server->setAppPath($this->apps_path . '/' . $this->current_app);
		// Let's load the app's modules
		$container = new ModuleContainer($server);
		if($this->main_conf->get('modules')
		   && is_array($this->main_conf->get('modules'))) {
            foreach($this->main_conf->get('modules') as $module) {
				$opts = NULL;
				if($this->main_conf->get($module)) {
                    // We give priority to the app's module configuration.
                    if($this->apps_conf[$this->current_app]->get($module)) {
                        $opts = $this->apps_conf[$this->current_app]->get($module);
                    } else {
                        $opts = $this->main_conf->get($module);
                    }
				}
				$container->addModule('Module_' . $module, $opts);
			}
		}

		$runner->setModules($container);
		$runner->serve($this->apps_conf[$this->current_app]->get('route'));
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
            return new Response($e->getCode() . " Error!", $e->getCode());
        }
    }
}

?>
