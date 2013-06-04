<?php

namespace assegai;

/**
 * Default controller for Assegai.
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
class Controller implements IController
{
    /** Object that contains loaded modules. */
	protected $modules;
    /** Server state variable. */
	protected $server;
    /** Current request object. */
	protected $request;
    /** Security provider. */
	protected $sec;

    /**
     * Controller's constructor. This is meant to be called by Core.
     * @param ModuleContainer $modules is a container of loaded modules.
     * @param Server $server is the current server state.
     * @param Request $request is the current request object.
     */
	public function __construct(\atlatl\ModuleContainer $modules,
                                \atlatl\Server $server,
                                \atlatl\Request $request,
                                \atlatl\Security $sec)
	{
		$this->modules = $modules;
		$this->server = $server;
        $this->sec = $sec;
		$this->request = new Request($request->allGet(),
                                     $request->allPost(),
                                     $this->sec);

        // Running the user init.
        $this->_init();
	}

    /**
     * This is run after the constructor. Implement to have custom code run.
     */
    protected function _init()
    {
    }

    /**
     * Loads a view.
     */
    protected function view($view_name, array $var_list = NULL)
    {
        if($var_list === NULL) {
            $var_list = array(); // Avoids notices.
        }
        $vars = (object)$var_list;

        if($hook_data = $this->modules->preView($this->request, $view_name, $vars)) {
            return $hook_data;
        }

        $serv = $this->server;
        $parent_tpl = false;
        $current_block = false;

        // Little hack to access urls easier.
        $url = function($url) use($serv) {
            return $serv->siteUrl($url);
        };

        $startblock = function($name) use(&$current_block) {
            $current_block = $name;
            ob_start();
        };

        $endblock = function() use(&$var_list, &$current_block) {
            $var_list[$current_block] = ob_get_clean();
            $current_block = false;
        };

        $inherit = function($template) use(&$parent_tpl) {
            $parent_tpl = $template;
        };
        
        $clean = function($val, $placeholder='-') {
            return \atlatl\Utils::cleanFilename($val, $placeholder);
        };

        $template_path = false;

        ob_start();

        $template_path = $this->server->getRelAppPath('views/' . $view_name . '.phtml');
        if(!file_exists($template_path)) {
            $template_path = $this->server->main->get('templates_path') . '/' . $view_name . '.phtml';
        }
        
        // Traditional PHP template.
        require($template_path);

        $data = ob_get_clean();

        if($hook_data = $this->modules->postView($this->request, $view_name, $vars, $data)) {
            return $hook_data;
        }

        if($parent_tpl) {
            return $this->view($parent_tpl, $var_list);
        }

        return $data;
    }

    /**
     * Loads a model.
     */
    protected function model($model_name)
    {
        if($hook_data = $this->modules->preModel($model_name)) {
            return $hook_data;
        }

        $model = new $model_name($this->modules);

        if($hook_data = $this->modules->postModel($model_name) === true) {
            return $hook_data;
        }

        return $model;
    }

    /**
     * Gets full path to app elements.
     * @param path is the relative path to resolve.
     * @return the corresponding absolute path.
     */
    protected function appPath($path)
    {
        return $this->app_path . '/' . $path;
    }

    /**
     * Tiny wrapper arround var_dump to ease debugging.
     * @param mixed $var is the variable to be dumped
     * @param boolean $no_html defines whether the variable contains
     * messy HTML characters or not. The given $var will be escaped if
     * set to false. Default is false.
     * @return The HTML code of a human representation of the $var.
     */
	protected function dump($var, $no_html = false)
	{
		$dump = var_export($var, true);
		if($no_html) {
			return $dump;
		} else {
			return '<pre>' . htmlentities($dump) . '</pre>' . PHP_EOL;;
		}
	}

	/**
	 * Method executed prior to any request handling.
	 */
	public function preRequest()
	{
	}

	/**
	 * Method executed following any request handling. This method is
	 * expected to return a Response object, which will then be sent
	 * back to the user.
	 * @param mixed $returned is the value that was previously returned
	 * by the routed method.
	 */
	public function postRequest($returned)
	{
		return $returned;
	}
}


?>
