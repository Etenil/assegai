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

namespace etenil\assegai;

use etenil\assegai\injector;
use etenil\assegai\Utils;
use etenil\assegai\Request;
use etenil\assegai\exceptions;

class Framework extends injector\Injectable
{
    protected $container;
    protected $conf;
    protected $apps;
    protected $loader;
    
    // Config things.
    protected $apps_path = 'apps';

    function __construct()
    {
        $this->apps = array();
    }

    function setConfig(Config $config)
    {
        $this->conf = $config;
    }
    
    function setAutoLoader(Autoloader $loader)
    {
        $this->autoloader = $loader;
    }

    function loadConfig($conf_path)
    {
        $this->conf->reset();
        $this->conf->loadFile($conf_path);
        
        $this->autoloader->setConf($this->conf);
        $this->autoloader->register();
        
        foreach($this->conf->get('apps', array()) as $appname) {
            $app = $this->container->give('app');
            $app->setName($appname);
            $app->setPath(Utils::joinPaths($this->conf->get('apps_path'), $appname));
            $this->apps[] = $app;
        }
    }

    function serve(Request $request = null)
    {
        try {
            $served = false;
            foreach($this->apps as $app) {
                $success = $app->serve($request);
                if($success) {
                    $served = true;
                    break;
                }
            }
            
            if(!$served) {
                throw new exceptions\HttpNotFoundError();
            }
        }
        catch(exceptions\HttpRedirect $r) {
            die('1');
        }
        catch(exceptions\HttpNotFoundError $e) {
            die('2');
        }
        catch(exceptions\HttpClientError $e) {
            die('3');
        }
        catch(exceptions\HttpServerError $e) {
            die('4');
        }
        // Generic HTTP status response.
        catch(exceptions\HttpStatus $s) {
            die('5');
        }
        catch(\Exception $e) {
            die('6');
        }
        
        // Well done...
    }

    function run()
    {
        $request = $this->container->give('request');
        $request->fromGlobals();
        
        $this->serve($request);
    }
}
