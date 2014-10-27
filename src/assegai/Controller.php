<?php

/**
 * Default controller for Assegai.
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

namespace assegai;

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

    /** Virtual methods. */
    protected $virtual_methods;
    protected $helpers;

    /**
     * Controller's constructor. This is meant to be called by Core.
     * @param ModuleContainer $modules is a container of loaded modules.
     * @param Server $server is the current server state.
     * @param Request $request is the current request object.
     */
	public function __construct(modules\ModuleContainer $modules,
                                Server $server,
                                eventsystem\events\IEvent $request,
                                Security $security)
	{
		$this->modules = $modules;
		$this->server = $server;
        $this->sec = $security;
        $this->request = $request;
        $this->helpers = array();

        // Running the user init.
        $this->_init();
	}

    /**
     * Register a method on the controller.
     *
     * Typically, this is used by modules to add their own custom methods
     * (or override default ones) to the controller. A good example of this
     * is the Forms module, which also hooks up to the autoloader to achieve
     * a very native feel.
     *
     * @param $name string the method's name. It must comply to the usual
     *      naming restrictions.
     * @param $callback callable is the function to assign to this method.
     */
    public function register($name, $callback)
    {
        $this->virtual_methods[$name] = $callback;
    }

    /**
     * Using a magic method is necessary to handle virtual methods calls;
     * although I really really hate PHP's magic stuff. Please remove me
     * once PHP will have cleared calls to closures stored in member variables.
     */
    function __call($method, $args)
    {
        if(array_key_exists($method, $this->virtual_methods)) {
            return call_user_func_array($this->virtual_methods[$method], $args);
        }
        else {
            throw new \Exception(sprintf("Unknown controller method called: %s::%s", get_class($this), $method));
        }
    }
	
	function redirect($to) {
        $response = new Response();
        $response->redirect($to);
        
        return $response;
    }

    /**
     * This is run after the constructor. Implement to have custom code run.
     * Be very careful, this is run before some module hooks, and may result in
     * missing methods or features. You may need to use the preRequest() method 
     * instead.
     */
    protected function _init()
    {
    }

    /**
     * Registers a new helper. Useful in modules.
     */
    function registerHelper($helper_name, IHelper $helper)
    {
        $this->helpers[$helper_name] = $helper;
        return $this;
    }

    /**
     * Instanciates a helper.
     */
    function helper($helper_name) {
        if(array_key_exists($helper_name, $this->helpers)) {
            return $this->helpers[$helper_name];
        }
        else {
            $classname = 'Helper_' . ucwords($helper_name);
            return new $classname($this->modules, $this->server, $this->request, $this->security);
        }
    }

    /**
     * Loads a view.
     */
    function view($view_name, array $var_list = NULL, array $block_list = NULL)
    {
        if($var_list === NULL) {
            $var_list = array(); // Avoids notices.
        }
        $vars = (object)$var_list;
        $blocks = (object)$block_list;

        if($hook_data = $this->modules->preView($this->request, $view_name, $vars)) {
            return $hook_data;
        }

        $serv = $this->server;
        $me = $this;
        $parent_tpl = false;
        $current_block = false;
        $helpers = new \stdClass();

        // Little hack to access urls easier.
        $url = function($url) use($serv) {
            return $serv->siteUrl($url);
        };

        $load_helper = function($helper_name) use(&$helpers, &$me) {
            $helpers->{$helper_name} = $me->helper($helper_name);
        };

        $startblock = function($name) use(&$current_block) {
            $current_block = $name;
            ob_start();
        };

        $endblock = function() use(&$block_list, &$current_block) {
            $block_list[$current_block] = ob_get_clean();
            $current_block = false;
        };

        $inherit = function($template) use(&$parent_tpl) {
            $parent_tpl = $template;
        };
        
        $clean = function($val, $placeholder='-') {
            return \assegai\Utils::cleanFilename($val, $placeholder);
        };

        $template_path = false;

        // Shorthands
        $h = &$helpers;

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
            return $this->view($parent_tpl, $var_list, $block_list);
        }

        return $data;
    }

    /**
     * Loads a model.
     */
    protected function model($model_name)
    {
        if(stripos($model_name, 'model') === false) {
            $model_name = sprintf('%s\models\%s', $this->server->getAppName(), $model_name);
        }

        if($hook_data = $this->modules->preModel($model_name)) {
            return $hook_data;
        }

        if(!class_exists($model_name)) {
            throw new exceptions\HttpInternalServerError("Class $model_name not found");
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
    
    /**
     * Generates a CSRF token.
     */
    protected final function csrf()
    {
        $token = Utils::randomString(64);
        $this->request->setSession('assegai_csrf_token', $token);
        return '<input type="hidden" name="csrf" value="' . $token . '" />';
    }
    
    /**
     * Checks the validity of the CSRF token.
     */
    protected final function checkCsrf()
    {
        $valid = false;
        
        $r = $this->request;
        
        if($r->getSession('assegai_csrf_token')
            && $r->post('csrf')
            && $r->getSession('assegai_csrf_token') == $r->post('csrf')) {
            $valid = true;
        }
        
        $this->request->killSession('assegai_csrf_token');
        return $valid;
    }
}

